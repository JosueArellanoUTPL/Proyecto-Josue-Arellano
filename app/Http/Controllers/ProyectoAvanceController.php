<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoAvance;
use App\Models\ProyectoAvanceEvidencia;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProyectoAvanceController extends Controller
{
    // Mostrar formulario para registrar avance.
    public function create(Proyecto $proyecto)
    {
        $proyecto->load(['programa.entidad']);

        return view('seguimiento.proyecto_avance_create', compact('proyecto'));
    }

    // Guardar avance de proyecto.
    public function store(Request $request, Proyecto $proyecto)
    {
        // Validacion del avance.
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'porcentaje_avance' => ['required', 'integer', 'min:0', 'max:100'],
            'comentario' => ['nullable', 'string', 'max:1000'],
            'evidencias' => ['nullable', 'array'],
            'evidencias.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $avance = ProyectoAvance::create([
            'proyecto_id' => $proyecto->id,
            'user_id' => Auth::id(),
            'fecha' => $data['fecha'],
            'porcentaje_avance' => (int) $data['porcentaje_avance'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        // Carga de evidencias.
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                if (! $file) {
                    continue;
                }

                $this->guardarEvidencia($avance, $file);
            }
        }

        return redirect()
            ->route('seguimiento.proyecto.show', $proyecto->id)
            ->with('success', 'Avance registrado correctamente.');
    }

    // Mostrar formulario para editar avance.
    public function edit(ProyectoAvance $avance)
    {
        $this->validarPropietario($avance);

        $avance->load([
            'proyecto.programa.entidad',
            'evidencias',
        ]);

        return view('seguimiento.proyecto_avance_edit', compact('avance'));
    }

    // Actualizar avance de proyecto.
    public function update(Request $request, ProyectoAvance $avance)
    {
        $this->validarPropietario($avance);

        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'porcentaje_avance' => ['required', 'integer', 'min:0', 'max:100'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        $avance->update([
            'fecha' => $data['fecha'],
            'porcentaje_avance' => (int) $data['porcentaje_avance'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        return redirect()
            ->route('seguimiento.proyecto.show', $avance->proyecto_id)
            ->with('success', 'Avance actualizado correctamente.');
    }

    // Agregar evidencia al avance.
    public function addEvidencia(Request $request, ProyectoAvance $avance)
    {
        $this->validarPropietario($avance);

        $request->validate([
            'evidencia' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // Carga de evidencia.
        $this->guardarEvidencia($avance, $request->file('evidencia'));

        return redirect()
            ->route('seguimiento.proyecto.show', $avance->proyecto_id)
            ->with('success', 'Evidencia agregada correctamente.');
    }

    // Eliminar evidencia del avance.
    public function deleteEvidencia(ProyectoAvanceEvidencia $evidencia)
    {
        $avance = $evidencia->avance;

        $this->validarPropietario($avance);

        // Eliminacion de evidencia.
        Storage::disk('public')->delete($evidencia->path);
        $evidencia->delete();

        return redirect()
            ->route('seguimiento.proyecto.show', $avance->proyecto_id)
            ->with('success', 'Evidencia eliminada correctamente.');
    }

    // Eliminar avance de proyecto.
    public function destroy(ProyectoAvance $avance)
    {
        $this->validarPropietario($avance);

        $proyectoId = $avance->proyecto_id;

        // Eliminacion de evidencias.
        foreach ($avance->evidencias as $evidencia) {
            Storage::disk('public')->delete($evidencia->path);
        }

        $avance->delete();

        return redirect()
            ->route('seguimiento.proyecto.show', $proyectoId)
            ->with('success', 'Avance eliminado correctamente.');
    }

    // Validacion de propietario.
    private function validarPropietario(ProyectoAvance $avance): void
    {
        if ($avance->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }
    }

    // Guardar evidencia.
    private function guardarEvidencia(ProyectoAvance $avance, UploadedFile $archivo): void
    {
        $ruta = $archivo->store('evidencias/proyectos', 'public');

        ProyectoAvanceEvidencia::create([
            'proyecto_avance_id' => $avance->id,
            'path' => $ruta,
            'original_name' => $archivo->getClientOriginalName(),
            'mime_type' => $archivo->getMimeType(),
            'size' => $archivo->getSize(),
        ]);
    }
}
