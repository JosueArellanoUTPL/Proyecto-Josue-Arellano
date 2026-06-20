<?php

namespace App\Http\Controllers;

use App\Models\Pdn;
use Illuminate\Http\Request;

class PdnController extends Controller
{
    public function index()
    {
        // Lista registros del PND.
        $items = Pdn::orderBy('id', 'desc')->get();
        return view('pdn.index', compact('items'));
    }

    public function create()
    {
        // Formulario para crear un PND.
        return view('pdn.create');
    }

    public function store(Request $request)
    {
        // Valida codigo, nombre y estado activo.
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:pdns,codigo',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        // Guarda el registro PND.
        Pdn::create($data);

        return redirect()->route('pdn.index')
            ->with('success', 'PND creado correctamente.');
    }

    public function edit(Pdn $pdn)
    {
        // En la vista lo manejo como item para reutilizar nombres.
        return view('pdn.edit', ['item' => $pdn]);
    }

    public function update(Request $request, Pdn $pdn)
    {
        // Valida antes de actualizar.
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:pdns,codigo,'.$pdn->id,
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        // Actualiza el registro.
        $pdn->update($data);

        return redirect()->route('pdn.index')
            ->with('success', 'PND actualizado correctamente.');
    }

    public function destroy(Pdn $pdn)
    {
        // Se desactiva para conservar planes y alineaciones históricas.
        $pdn->update(['activo' => false]);

        return redirect()->route('pdn.index')
            ->with('success', 'PND desactivado correctamente.');
    }
}
