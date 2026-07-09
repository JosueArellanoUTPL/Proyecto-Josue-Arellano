<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use App\Models\IndicadorAvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AvanceIndicadorController extends Controller
{
    // Mostrar formulario para registrar avance.
    public function create(Indicador $indicador)
    {
        $indicador->load('meta.plan');

        return view('seguimiento.indicador_avance', compact('indicador'));
    }

    // Guardar avance de indicador.
    public function store(Request $request, Indicador $indicador)
    {
        // Validacion del avance.
        $data = $this->validarAvance($request);

        // Carga de evidencia.
        $path = null;
        if ($request->hasFile('evidencia')) {
            $path = $request->file('evidencia')->store('evidencias', 'public');
        }

        IndicadorAvance::create([
            'indicador_id' => $indicador->id,
            'user_id' => Auth::id(),
            'fecha' => $data['fecha'],
            'valor_reportado' => (int) $data['valor_reportado'],
            'comentario' => $data['comentario'] ?? null,
            'evidencia_path' => $path,
        ]);

        return redirect()
            ->route('seguimiento.meta.show', $indicador->meta_id)
            ->with('success', 'Avance registrado correctamente.');
    }

    // Mostrar formulario para editar avance.
    public function edit(IndicadorAvance $avance)
    {
        $this->validarPropietario($avance);

        $avance->load('indicador.meta.plan');

        return view('seguimiento.indicador_avance_edit', compact('avance'));
    }

    // Actualizar avance de indicador.
    public function update(Request $request, IndicadorAvance $avance)
    {
        $this->validarPropietario($avance);

        $data = $this->validarAvance($request);

        // Reemplazo de evidencia.
        if ($request->hasFile('evidencia')) {
            if ($avance->evidencia_path) {
                Storage::disk('public')->delete($avance->evidencia_path);
            }
            $avance->evidencia_path = $request->file('evidencia')->store('evidencias', 'public');
        }

        $avance->update([
            'fecha' => $data['fecha'],
            'valor_reportado' => (int) $data['valor_reportado'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        return redirect()
            ->route('seguimiento.meta.show', $avance->indicador->meta_id)
            ->with('success', 'Avance actualizado correctamente.');
    }

    // Eliminar avance de indicador.
    public function destroy(IndicadorAvance $avance)
    {
        $this->validarPropietario($avance);

        // Eliminacion de evidencia.
        if ($avance->evidencia_path) {
            Storage::disk('public')->delete($avance->evidencia_path);
        }

        $metaId = $avance->indicador->meta_id;
        $avance->delete();

        return redirect()
            ->route('seguimiento.meta.show', $metaId)
            ->with('success', 'Avance eliminado correctamente.');
    }

    // Validacion del avance.
    private function validarAvance(Request $request): array
    {
        return $request->validate([
            'fecha' => ['required', 'date'],
            'valor_reportado' => ['required', 'integer'],
            'comentario' => ['nullable', 'string'],
            'evidencia' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);
    }

    // Validacion de propietario.
    private function validarPropietario(IndicadorAvance $avance): void
    {
        if ($avance->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }
    }
}
