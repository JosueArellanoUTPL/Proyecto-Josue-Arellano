<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoAvance;
use App\Models\ProyectoAvanceEvidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProyectoAvanceController extends Controller
{
    public function create(Proyecto $proyecto)
    {
        // Muestra datos del proyecto para registrar un avance con contexto.
        $proyecto->load(['programa.entidad']);

        return view('seguimiento.proyecto_avance_create', compact('proyecto'));
    }

    public function store(Request $request, Proyecto $proyecto)
    {
        // Valida porcentaje, fecha, comentario y evidencias opcionales.
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'porcentaje_avance' => ['required', 'numeric', 'min:0', 'max:100'],
            'comentario' => ['nullable', 'string', 'max:1000'],
            'evidencias' => ['nullable', 'array'],
            'evidencias.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // Crea el avance principal y guarda quien lo registro.
        $avance = ProyectoAvance::create([
            'proyecto_id' => $proyecto->id,
            'user_id' => Auth::id(),
            'fecha' => $data['fecha'],
            'porcentaje_avance' => $data['porcentaje_avance'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        // Si vienen evidencias, se guardan una por una.
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

    public function edit(ProyectoAvance $avance)
    {
        // Solo el dueno del avance o el admin puede editar.
        if ($avance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $avance->load([
            'proyecto.programa.entidad',
            'evidencias'
        ]);

        return view('seguimiento.proyecto_avance_edit', compact('avance'));
    }

    public function update(Request $request, ProyectoAvance $avance)
    {
        // Repite la proteccion: solo dueno o admin.
        if ($avance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'porcentaje_avance' => ['required', 'numeric', 'min:0', 'max:100'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        // Actualiza solo datos principales, no toca evidencias.
        $avance->update([
            'fecha' => $data['fecha'],
            'porcentaje_avance' => $data['porcentaje_avance'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        return redirect()
            ->route('seguimiento.proyecto.show', $avance->proyecto_id)
            ->with('success', 'Avance actualizado correctamente.');
    }

    public function addEvidencia(Request $request, ProyectoAvance $avance)
    {
        // Solo el dueno o admin puede agregar evidencia al avance.
        if ($avance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'evidencia' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // Guarda el archivo y luego guarda su informacion en la tabla.
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

    public function deleteEvidencia(ProyectoAvanceEvidencia $evidencia)
    {
        $avance = $evidencia->avance;

        // Solo el dueno del avance o admin puede borrar evidencias.
        if ($avance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Borra archivo fisico y registro de base.
        Storage::disk('public')->delete($evidencia->path);
        $evidencia->delete();

        return redirect()
            ->route('seguimiento.proyecto.show', $avance->proyecto_id)
            ->with('success', 'Evidencia eliminada correctamente.');
    }

    public function destroy(ProyectoAvance $avance)
    {
        // Solo el dueno del avance o admin puede eliminar el avance completo.
        if ($avance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $proyectoId = $avance->proyecto_id;

        // Antes de borrar el avance, borra sus evidencias del storage.
        foreach ($avance->evidencias as $ev) {
            Storage::disk('public')->delete($ev->path);
        }

        $avance->delete();

        return redirect()
            ->route('seguimiento.proyecto.show', $proyectoId)
            ->with('success', 'Avance eliminado correctamente.');
    }
}
