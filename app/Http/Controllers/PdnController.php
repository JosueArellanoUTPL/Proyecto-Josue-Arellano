<?php

namespace App\Http\Controllers;

use App\Models\Pdn;
use Illuminate\Http\Request;

class PdnController extends Controller
{
    public function index()
    {
        $items = Pdn::orderBy('id', 'desc')->get();
        return view('pdn.index', compact('items'));
    }

    public function create()
    {
        return view('pdn.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:20',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        Pdn::create($data);

        return redirect()->route('pdn.index')
            ->with('success', 'Registro PND/PDN creado correctamente.');
    }

    public function edit(Pdn $pdn)
    {
        return view('pdn.edit', ['item' => $pdn]);
    }

    public function update(Request $request, Pdn $pdn)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:20',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        $pdn->update($data);

        return redirect()->route('pdn.index')
            ->with('success', 'Registro PND/PDN actualizado correctamente.');
    }

    public function destroy(Pdn $pdn)
    {
        $pdn->delete();

        return redirect()->route('pdn.index')
            ->with('success', 'Registro PND/PDN eliminado correctamente.');
    }
}
