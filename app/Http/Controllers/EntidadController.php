<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use Illuminate\Http\Request;

class EntidadController extends Controller
{
    public function index()
    {
        // Lista entidades en la pantalla principal.
        $entidades = Entidad::orderBy('id', 'desc')->paginate(10);
        return view('entidades.index', compact('entidades'));
    }

    public function create()
    {
        // Muestra el formulario para crear entidad.
        return view('entidades.create');
    }

    public function store(Request $request)
    {
        // Valida los campos del formulario.
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:entidades,codigo'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable'],
        ]);

        // Convierte el checkbox activo en true/false.
        $validated['activo'] = $request->has('activo');

        // Guarda la entidad.
        Entidad::create($validated);

        return redirect()->route('entidades.index')
            ->with('success', 'Entidad creada correctamente.');
    }

    public function edit(Entidad $entidade)
    {
        // Laravel usa $entidade; lo renombro para que la vista sea mas clara.
        $entidad = $entidade;
        return view('entidades.edit', compact('entidad'));
    }

    public function update(Request $request, Entidad $entidade)
    {
        // Valida antes de actualizar.
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:entidades,codigo,'.$entidade->id],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable'],
        ]);

        $validated['activo'] = $request->has('activo');

        // Actualiza la entidad seleccionada.
        $entidade->update($validated);

        return redirect()->route('entidades.index')
            ->with('success', 'Entidad actualizada correctamente.');
    }

    public function destroy(Entidad $entidade)
    {
        // Se desactiva para conservar planes, programas y proyectos relacionados.
        $entidade->update(['activo' => false]);

        return redirect()->route('entidades.index')
            ->with('success', 'Entidad desactivada correctamente.');
    }
}
