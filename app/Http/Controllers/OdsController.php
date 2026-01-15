<?php

namespace App\Http\Controllers;

use App\Models\Ods;
use Illuminate\Http\Request;

class OdsController extends Controller
{
    public function index()
    {
        $items = Ods::orderBy('id', 'desc')->get();
        return view('ods.index', compact('items'));
    }

    public function create()
    {
        return view('ods.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:10',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        Ods::create($data);

        return redirect()->route('ods.index')
            ->with('success', 'ODS creado correctamente.');
    }

    public function edit(Ods $od)
    {
        return view('ods.edit', ['item' => $od]);
    }

    public function update(Request $request, Ods $od)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:10',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);

        $od->update($data);

        return redirect()->route('ods.index')
            ->with('success', 'ODS actualizado correctamente.');
    }

    public function destroy(Ods $od)
    {
        $od->delete();

        return redirect()->route('ods.index')
            ->with('success', 'ODS eliminado correctamente.');
    }
}
