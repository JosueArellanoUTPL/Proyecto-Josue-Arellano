<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use Illuminate\Http\Request;

class EntidadController extends Controller
{
    // Listar entidades.
    public function index()
    {
        $entidades = Entidad::orderBy('id', 'desc')->paginate(10);

        return view('entidades.index', compact('entidades'));
    }

    // Mostrar formulario para crear entidad.
    public function create()
    {
        return view('entidades.create');
    }

    // Guardar entidad.
    public function store(Request $request)
    {
        // Validacion de entidad.
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:entidades,codigo'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable'],
        ]);

        // Estado activo.
        $validated['activo'] = $request->has('activo');

        Entidad::create($validated);

        return redirect()->route('entidades.index')
            ->with('success', 'Entidad creada correctamente.');
    }

    // Mostrar formulario para editar entidad.
    public function edit(Entidad $entidade)
    {
        $entidad = $entidade;

        return view('entidades.edit', compact('entidad'));
    }

    // Actualizar entidad.
    public function update(Request $request, Entidad $entidade)
    {
        // Validacion de entidad.
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:entidades,codigo,'.$entidade->id],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable'],
        ]);

        $validated['activo'] = $request->has('activo');

        $entidade->update($validated);

        return redirect()->route('entidades.index')
            ->with('success', 'Entidad actualizada correctamente.');
    }

    // Desactivar entidad.
    public function destroy(Entidad $entidade)
    {
        // Desactivacion para conservar relaciones.
        $entidade->update(['activo' => false]);

        return redirect()->route('entidades.index')
            ->with('success', 'Entidad desactivada correctamente.');
    }
}
