<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use App\Models\IndicadorAvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AvanceIndicadorController extends Controller
{
    public function create(Indicador $indicador)
    {
        // Carga la meta y el plan para mostrar contexto en el formulario.
        $indicador->load('meta.plan');
        return view('seguimiento.indicador_avance', compact('indicador'));
    }

    public function store(Request $request, Indicador $indicador)
    {
        // Valida avance, comentario y evidencia opcional.
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'valor_reportado' => ['required', 'numeric'],
            'comentario' => ['nullable', 'string'],
            'evidencia' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // Si hay evidencia, se guarda en storage/app/public/evidencias.
        $path = null;
        if ($request->hasFile('evidencia')) {
            $path = $request->file('evidencia')->store('evidencias', 'public');
        }

        // Guarda el avance y el usuario que lo registro.
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
        // Solo el dueno del avance o el admin puede editar.
        if ($avance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $avance->load('indicador.meta.plan');

        return view('seguimiento.indicador_avance_edit', compact('avance'));
    }

    public function update(Request $request, IndicadorAvance $avance)
    {
        // Repite la regla: solo dueno o admin.
        if ($avance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'valor_reportado' => ['required', 'numeric'],
            'comentario' => ['nullable', 'string'],
            'evidencia' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // Si sube una evidencia nueva, borra la anterior para no dejar basura.
        if ($request->hasFile('evidencia')) {
            if ($avance->evidencia_path) {
                Storage::disk('public')->delete($avance->evidencia_path);
            }
            $avance->evidencia_path = $request->file('evidencia')->store('evidencias', 'public');
        }

        // Actualiza los campos principales del avance.
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
        // Solo el dueno del avance o el admin puede eliminar.
        if ($avance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Borra el archivo de evidencia si existia.
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
