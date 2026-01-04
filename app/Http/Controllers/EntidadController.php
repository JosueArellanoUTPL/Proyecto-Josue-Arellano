<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use Illuminate\Http\Request;

class EntidadController extends Controller
{
    public function index()
    {
        $entidades = Entidad::orderBy('id', 'desc')->paginate(10);
        return view('entidades.index', compact('entidades'));
    }

    public function create()
    {
        return view('entidades.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable'], // checkbox
        ]);

        $validated['activo'] = $request->has('activo');

        Entidad::create($validated);

        return redirect()->route('entidades.index')
            ->with('success', 'Entidad creada correctamente.');
    }

    public function show(Entidad $entidade) // ojo: Laravel puede nombrarlo raro
    {
        // Para evitar problemas, mejor NO uses show en este módulo (opcional).
        return redirect()->route('entidades.index');
    }

    public function edit(Entidad $entidade)
    {
        $entidad = $entidade;
        return view('entidades.edit', compact('entidad'));
    }

    public function update(Request $request, Entidad $entidade)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable'],
        ]);

        $validated['activo'] = $request->has('activo');

        $entidade->update($validated);

        return redirect()->route('entidades.index')
            ->with('success', 'Entidad actualizada correctamente.');
    }

    public function destroy(Entidad $entidade)
    {
        $entidade->delete();

        return redirect()->route('entidades.index')
            ->with('success', 'Entidad eliminada correctamente.');
    }
}
