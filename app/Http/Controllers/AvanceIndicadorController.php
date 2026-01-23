<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use App\Models\IndicadorAvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador de avances de indicadores.
 *
 * Permite:
 * - registrar avances
 * - editar avances existentes
 * - eliminar avances y evidencias
 *
 * El progreso de indicadores y metas se recalcula automáticamente
 * a partir de los avances.
 */
class AvanceIndicadorController extends Controller
{
    public function create(Indicador $indicador)
    {
        $indicador->load('meta.plan');
        return view('seguimiento.indicador_avance', compact('indicador'));
    }

    public function store(Request $request, Indicador $indicador)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'valor_reportado' => ['required', 'numeric'],
            'comentario' => ['nullable', 'string'],
            'evidencia' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $path = null;
        if ($request->hasFile('evidencia')) {
            $path = $request->file('evidencia')->store('evidencias', 'public');
        }

        IndicadorAvance::create([
            'indicador_id' => $indicador->id,
            'user_id' => Auth::id(),
            'fecha' => $data['fecha'],
            'valor_reportado' => $data['valor_reportado'],
            'comentario' => $data['comentario'] ?? null,
            'evidencia_path' => $path,
        ]);

        return redirect()
            ->route('seguimiento.meta.show', $indicador->meta_id)
            ->with('success', 'Avance registrado correctamente.');
    }

    public function edit(IndicadorAvance $avance)
    {
        if ($avance->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $avance->load('indicador.meta.plan');

        return view('seguimiento.indicador_avance_edit', compact('avance'));
    }

    public function update(Request $request, IndicadorAvance $avance)
    {
        if ($avance->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'valor_reportado' => ['required', 'numeric'],
            'comentario' => ['nullable', 'string'],
            'evidencia' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($request->hasFile('evidencia')) {
            if ($avance->evidencia_path) {
                Storage::disk('public')->delete($avance->evidencia_path);
            }
            $avance->evidencia_path = $request->file('evidencia')->store('evidencias', 'public');
        }

        $avance->update([
            'fecha' => $data['fecha'],
            'valor_reportado' => $data['valor_reportado'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        return redirect()
            ->route('seguimiento.meta.show', $avance->indicador->meta_id)
            ->with('success', 'Avance actualizado correctamente.');
    }

    public function destroy(IndicadorAvance $avance)
    {
        if ($avance->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        if ($avance->evidencia_path) {
            Storage::disk('public')->delete($avance->evidencia_path);
        }

        $metaId = $avance->indicador->meta_id;
        $avance->delete();

        return redirect()
            ->route('seguimiento.meta.show', $metaId)
            ->with('success', 'Avance eliminado correctamente.');
    }
}
