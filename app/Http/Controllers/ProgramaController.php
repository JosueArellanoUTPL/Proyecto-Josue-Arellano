<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    public function index()
    {
        $programas = Programa::orderBy('id', 'desc')->paginate(10);
        return view('programas.index', compact('programas'));
    }

    public function create()
    {
        return view('programas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable'],
        ]);

        $validated['activo'] = $request->has('activo');

        Programa::create($validated);

        return redirect()->route('programas.index')
            ->with('success', 'Programa creado correctamente.');
    }

    public function edit(Programa $programa)
    {
        return view('programas.edit', compact('programa'));
    }

    public function update(Request $request, Programa $programa)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable'],
        ]);

        $validated['activo'] = $request->has('activo');

        $programa->update($validated);

        return redirect()->route('programas.index')
            ->with('success', 'Programa actualizado correctamente.');
    }

    public function destroy(Programa $programa)
    {
        $programa->delete();

        return redirect()->route('programas.index')
            ->with('success', 'Programa eliminado correctamente.');
    }
}
