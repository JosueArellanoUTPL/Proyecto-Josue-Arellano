<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\Entidad;
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
        $entidades = Entidad::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        return view('programas.create', compact('entidades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entidad_id'  => ['required', 'exists:entidades,id'],
            'nombre'      => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo'      => ['nullable'],
        ]);

        $validated['activo'] = $request->has('activo');

        Programa::create($validated);

        return redirect()->route('programas.index')
            ->with('success', 'Programa creado correctamente.');
    }

    public function edit(Programa $programa)
    {
        $entidades = Entidad::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        return view('programas.edit', compact('programa', 'entidades'));
    }

    public function update(Request $request, Programa $programa)
    {
        $validated = $request->validate([
            'entidad_id'  => ['required', 'exists:entidades,id'],
            'nombre'      => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo'      => ['nullable'],
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
