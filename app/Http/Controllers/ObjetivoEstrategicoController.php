<?php

namespace App\Http\Controllers;

use App\Models\ObjetivoEstrategico;
use Illuminate\Http\Request;

class ObjetivoEstrategicoController extends Controller
{
    // Listar objetivos estrategicos.
    public function index()
    {
        $objetivos = ObjetivoEstrategico::orderBy('id', 'desc')->get();

        return view('objetivos-estrategicos.index', compact('objetivos'));
    }

    // Mostrar formulario para crear objetivo.
    public function create()
    {
        return view('objetivos-estrategicos.create');
    }

    // Guardar objetivo estrategico.
    public function store(Request $request)
    {
        // Validacion de objetivo estrategico.
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:objetivos_estrategicos,codigo',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        ObjetivoEstrategico::create($data);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estrategico creado correctamente.');
    }

    // Mostrar formulario para editar objetivo.
    public function edit(ObjetivoEstrategico $objetivo)
    {
        return view('objetivos-estrategicos.edit', compact('objetivo'));
    }

    // Actualizar objetivo estrategico.
    public function update(Request $request, ObjetivoEstrategico $objetivo)
    {
        // Validacion de objetivo estrategico.
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:objetivos_estrategicos,codigo,'.$objetivo->id,
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        $objetivo->update($data);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estrategico actualizado correctamente.');
    }

    // Desactivar objetivo estrategico.
    public function destroy(ObjetivoEstrategico $objetivo)
    {
        // Desactivacion para conservar alineaciones.
        $objetivo->update(['activo' => false]);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estratégico desactivado correctamente.');
    }
}
