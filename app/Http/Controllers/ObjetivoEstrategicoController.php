<?php

namespace App\Http\Controllers;

use App\Models\ObjetivoEstrategico;
use Illuminate\Http\Request;

class ObjetivoEstrategicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = ObjetivoEstrategico::orderBy('id', 'desc')->get();
        return view('objetivos_estrategicos.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('objetivos_estrategicos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        ObjetivoEstrategico::create($data);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estratégico creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ObjetivoEstrategico $objetivos_estrategico)
    {
        return view('objetivos_estrategicos.edit', [
            'item' => $objetivos_estrategico
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ObjetivoEstrategico $objetivos_estrategico)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        $objetivos_estrategico->update($data);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estratégico actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ObjetivoEstrategico $objetivos_estrategico)
    {
        $objetivos_estrategico->delete();

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estratégico eliminado correctamente.');
    }
}
