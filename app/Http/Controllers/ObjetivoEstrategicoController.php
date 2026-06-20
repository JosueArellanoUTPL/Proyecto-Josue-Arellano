<?php

namespace App\Http\Controllers;

use App\Models\ObjetivoEstrategico;
use Illuminate\Http\Request;

class ObjetivoEstrategicoController extends Controller
{
    // Listar objetivos estrategicos.
    public function index()
    {
        $items = ObjetivoEstrategico::orderBy('id', 'desc')->get();

        return view('objetivos_estrategicos.index', compact('items'));
    }

    // Mostrar formulario para crear objetivo.
    public function create()
    {
        return view('objetivos_estrategicos.create');
    }

    // Guardar objetivo estrategico.
    public function store(Request $request)
    {
        // Validacion de objetivo estrategico.
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:objetivo_estrategicos,codigo',
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
    public function edit(ObjetivoEstrategico $objetivos_estrategico)
    {
        return view('objetivos_estrategicos.edit', [
            'item' => $objetivos_estrategico,
        ]);
    }

    // Actualizar objetivo estrategico.
    public function update(Request $request, ObjetivoEstrategico $objetivos_estrategico)
    {
        // Validacion de objetivo estrategico.
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:objetivo_estrategicos,codigo,'.$objetivos_estrategico->id,
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        $objetivos_estrategico->update($data);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estrategico actualizado correctamente.');
    }

    // Desactivar objetivo estrategico.
    public function destroy(ObjetivoEstrategico $objetivos_estrategico)
    {
        // Desactivacion para conservar alineaciones.
        $objetivos_estrategico->update(['activo' => false]);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estratégico desactivado correctamente.');
    }
}
