<?php

namespace App\Http\Controllers;

use App\Models\ObjetivoEstrategico;
use Illuminate\Http\Request;

class ObjetivoEstrategicoController extends Controller
{
    public function index()
    {
        // Lista objetivos estrategicos registrados.
        $items = ObjetivoEstrategico::orderBy('id', 'desc')->get();
        return view('objetivos_estrategicos.index', compact('items'));
    }

    public function create()
    {
        // Formulario para crear objetivo estrategico.
        return view('objetivos_estrategicos.create');
    }

    public function store(Request $request)
    {
        // Valida nombre, descripcion y estado.
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:objetivo_estrategicos,codigo',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        // Guarda el objetivo estrategico.
        ObjetivoEstrategico::create($data);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estrategico creado correctamente.');
    }

    public function edit(ObjetivoEstrategico $objetivos_estrategico)
    {
        // En la vista lo manejo como item para reutilizar nombres.
        return view('objetivos_estrategicos.edit', [
            'item' => $objetivos_estrategico
        ]);
    }

    public function update(Request $request, ObjetivoEstrategico $objetivos_estrategico)
    {
        // Valida antes de actualizar.
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:objetivo_estrategicos,codigo,'.$objetivos_estrategico->id,
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        // Actualiza el objetivo estrategico.
        $objetivos_estrategico->update($data);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estrategico actualizado correctamente.');
    }

    public function destroy(ObjetivoEstrategico $objetivos_estrategico)
    {
        // Se desactiva para conservar las alineaciones históricas.
        $objetivos_estrategico->update(['activo' => false]);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estratégico desactivado correctamente.');
    }
}
