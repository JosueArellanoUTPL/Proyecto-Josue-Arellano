<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\Entidad;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    public function index()
    {
        // Lista programas en la tabla principal.
        $programas = Programa::orderBy('id', 'desc')->paginate(10);
        return view('programas.index', compact('programas'));
    }

    public function create()
    {
        // Entidades activas para asociar el programa.
        $entidades = Entidad::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        return view('programas.create', compact('entidades'));
    }

    public function store(Request $request)
    {
        // Valida el formulario antes de crear.
        $validated = $request->validate([
            'codigo'      => ['required', 'string', 'max:30', 'unique:programas,codigo'],
            'entidad_id'  => ['required', 'exists:entidades,id'],
            'nombre'      => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo'      => ['nullable'],
        ]);

        // Checkbox activo convertido a true/false.
        $validated['activo'] = $request->has('activo');

        // Guarda el programa.
        Programa::create($validated);

        return redirect()->route('programas.index')
            ->with('success', 'Programa creado correctamente.');
    }

    public function edit(Programa $programa)
    {
        // Entidades disponibles para cambiar la asociacion.
        $entidades = Entidad::where('activo', 1)
            ->orWhere('id', $programa->entidad_id)
            ->orderBy('nombre')
            ->get();

        return view('programas.edit', compact('programa', 'entidades'));
    }

    public function update(Request $request, Programa $programa)
    {
        // Valida antes de actualizar.
        $validated = $request->validate([
            'codigo'      => ['required', 'string', 'max:30', 'unique:programas,codigo,'.$programa->id],
            'entidad_id'  => ['required', 'exists:entidades,id'],
            'nombre'      => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo'      => ['nullable'],
        ]);

        $validated['activo'] = $request->has('activo');

        // Un programa con proyectos no puede moverse a otra entidad.
        if ((int) $programa->entidad_id !== (int) $validated['entidad_id'] && $programa->proyectos()->exists()) {
            return back()
                ->withErrors(['entidad_id' => 'No puedes cambiar la entidad porque el programa ya tiene proyectos.'])
                ->withInput();
        }

        // Actualiza el programa seleccionado.
        $programa->update($validated);

        return redirect()->route('programas.index')
            ->with('success', 'Programa actualizado correctamente.');
    }

    public function destroy(Programa $programa)
    {
        // Se desactiva para conservar sus proyectos y avances.
        $programa->update(['activo' => false]);

        return redirect()->route('programas.index')
            ->with('success', 'Programa desactivado correctamente.');
    }
}
