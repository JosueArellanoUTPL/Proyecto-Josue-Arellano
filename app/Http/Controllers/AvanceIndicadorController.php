<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use App\Models\IndicadorAvance;
use Illuminate\Http\Request;

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
            'fecha' => 'required|date',
            'valor_reportado' => 'required|numeric',
            'comentario' => 'nullable|string',
            'evidencia' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('evidencia')) {
            $path = $request->file('evidencia')->store('evidencias', 'public');
        }

        IndicadorAvance::create([
            'indicador_id' => $indicador->id,
            'user_id' => auth()->id(),
            'fecha' => $data['fecha'],
            'valor_reportado' => $data['valor_reportado'],
            'comentario' => $data['comentario'] ?? null,
            'evidencia_path' => $path,
        ]);

        return redirect()->route('seguimiento.meta.show', $indicador->meta_id)
            ->with('success', 'Avance registrado correctamente.');
    }
}
