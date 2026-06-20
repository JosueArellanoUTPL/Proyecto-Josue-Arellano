<?php

namespace App\Http\Controllers;

use App\Models\Ods;
use Illuminate\Http\Request;

class OdsController extends Controller
{
    public function index()
    {
        // Lista ODS registrados.
        $items = Ods::orderBy('id', 'desc')->get();
        return view('ods.index', compact('items'));
    }

    public function create()
    {
        // Formulario para crear ODS.
        return view('ods.create');
    }

    public function store(Request $request)
    {
        // Valida codigo, nombre y estado activo.
        $data = $request->validate([
            'codigo' => 'required|string|max:10|unique:ods,codigo',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        // Guarda el ODS.
        Ods::create($data);

        return redirect()->route('ods.index')
            ->with('success', 'ODS creado correctamente.');
    }

    public function edit(Ods $od)
    {
        // En la vista lo manejo como item para reutilizar nombres.
        return view('ods.edit', ['item' => $od]);
    }

    public function update(Request $request, Ods $od)
    {
        // Valida antes de actualizar.
        $data = $request->validate([
            'codigo' => 'required|string|max:10|unique:ods,codigo,'.$od->id,
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        // Actualiza el ODS.
        $od->update($data);

        return redirect()->route('ods.index')
            ->with('success', 'ODS actualizado correctamente.');
    }

    public function destroy(Ods $od)
    {
        // Se desactiva para conservar las alineaciones históricas.
        $od->update(['activo' => false]);

        return redirect()->route('ods.index')
            ->with('success', 'ODS desactivado correctamente.');
    }
}
