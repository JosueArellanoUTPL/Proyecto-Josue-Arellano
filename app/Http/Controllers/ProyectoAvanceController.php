<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoAvance;
use App\Models\ProyectoAvanceEvidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador de Avances de Proyecto
 *
 * Aquí se gestiona el seguimiento real de los proyectos:
 * - Registro de avances (porcentaje + comentario)
 * - Edición y eliminación de avances
 * - Gestión de evidencias como archivos independientes
 *
 * Importante:
 * Cada evidencia se guarda como un registro separado, lo que permite
 * agregar nuevas evidencias con el tiempo sin sobrescribir las anteriores.
 */
class ProyectoAvanceController extends Controller
{
    /**
     * Muestra el formulario para registrar un nuevo avance del proyecto.
     * Se carga la entidad y el programa solo para mostrar contexto en la vista.
     */
    public function create(Proyecto $proyecto)
    {
        $proyecto->load(['entidad', 'programa']);

        return view('seguimiento.proyecto_avance_create', compact('proyecto'));
    }

    /**
     * Guarda un nuevo avance del proyecto.
     *
     * - Registra fecha, porcentaje y comentario
     * - El porcentaje representa el estado del proyecto en ese momento
     * - Las evidencias iniciales (si se envían) se almacenan una por una
     */
    public function store(Request $request, Proyecto $proyecto)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'porcentaje_avance' => ['required', 'numeric', 'min:0', 'max:100'],
            'comentario' => ['nullable', 'string', 'max:1000'],

            // Evidencias opcionales: cada archivo se guarda como registro independiente
            'evidencias' => ['nullable', 'array'],
            'evidencias.*' => ['file', 'max:5120'], // 5 MB por archivo
        ]);

        // Se crea el avance principal del proyecto
        $avance = ProyectoAvance::create([
            'proyecto_id' => $proyecto->id,
            'user_id' => Auth::id(),
            'fecha' => $data['fecha'],
            'porcentaje_avance' => $data['porcentaje_avance'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        // Si se suben evidencias al mismo tiempo, se guardan una por una
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                if (!$file) {
                    continue;
                }

                $path = $file->store('evidencias/proyectos', 'public');

                ProyectoAvanceEvidencia::create([
                    'proyecto_avance_id' => $avance->id,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('seguimiento.proyecto.show', $proyecto->id)
            ->with('success', 'Avance registrado correctamente.');
    }

    /**
     * Muestra el formulario de edición de un avance.
     * Solo puede acceder el usuario que creó el avance o un administrador.
     */
    public function edit(ProyectoAvance $avance)
    {
        if ($avance->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $avance->load([
            'proyecto.entidad',
            'proyecto.programa',
            'evidencias'
        ]);

        return view('seguimiento.proyecto_avance_edit', compact('avance'));
    }

    /**
     * Actualiza los datos del avance.
     * No se eliminan evidencias aquí, solo se modifican los campos principales.
     */
    public function update(Request $request, ProyectoAvance $avance)
    {
        if ($avance->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'porcentaje_avance' => ['required', 'numeric', 'min:0', 'max:100'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        $avance->update([
            'fecha' => $data['fecha'],
            'porcentaje_avance' => $data['porcentaje_avance'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        return redirect()
            ->route('seguimiento.proyecto.show', $avance->proyecto_id)
            ->with('success', 'Avance actualizado correctamente.');
    }

    /**
     * Agrega una evidencia a un avance ya existente.
     * Esto permite subir evidencias en distintos momentos sin sobrescribir.
     */
    public function addEvidencia(Request $request, ProyectoAvance $avance)
    {
        if ($avance->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'evidencia' => ['required', 'file', 'max:5120'],
        ]);

        $file = $request->file('evidencia');
        $path = $file->store('evidencias/proyectos', 'public');

        ProyectoAvanceEvidencia::create([
            'proyecto_avance_id' => $avance->id,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return redirect()
            ->route('seguimiento.proyecto.show', $avance->proyecto_id)
            ->with('success', 'Evidencia agregada correctamente.');
    }

    /**
     * Elimina una evidencia individual.
     * Se borra tanto el archivo físico como el registro en base de datos.
     */
    public function deleteEvidencia(ProyectoAvanceEvidencia $evidencia)
    {
        $avance = $evidencia->avance;

        if ($avance->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        Storage::disk('public')->delete($evidencia->path);
        $evidencia->delete();

        return redirect()
            ->route('seguimiento.proyecto.show', $avance->proyecto_id)
            ->with('success', 'Evidencia eliminada correctamente.');
    }

    /**
     * Elimina un avance completo.
     * Antes de eliminar el registro, se eliminan todas sus evidencias físicas.
     */
    public function destroy(ProyectoAvance $avance)
    {
        if ($avance->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $proyectoId = $avance->proyecto_id;

        foreach ($avance->evidencias as $ev) {
            Storage::disk('public')->delete($ev->path);
        }

        $avance->delete();

        return redirect()
            ->route('seguimiento.proyecto.show', $proyectoId)
            ->with('success', 'Avance eliminado correctamente.');
    }
}

