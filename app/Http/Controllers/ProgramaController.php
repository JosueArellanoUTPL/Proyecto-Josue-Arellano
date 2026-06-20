<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use App\Models\Programa;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    // Listar programas.
    public function index()
    {
        $programas = Programa::orderBy('id', 'desc')->paginate(10);

        return view('programas.index', compact('programas'));
    }

    // Mostrar formulario para crear programa.
    public function create()
    {
        $entidades = Entidad::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        return view('programas.create', compact('entidades'));
    }

    // Guardar programa.
    public function store(Request $request)
    {
        // Validacion de programa.
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'unique:programas,codigo'],
            'entidad_id' => ['required', 'exists:entidades,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable'],
        ]);

        // Estado activo.
        $validated['activo'] = $request->has('activo');

        Programa::create($validated);

        return redirect()->route('programas.index')
            ->with('success', 'Programa creado correctamente.');
    }

    // Mostrar formulario para editar programa.
    public function edit(Programa $programa)
    {
        $entidades = Entidad::where('activo', 1)
            ->orWhere('id', $programa->entidad_id)
            ->orderBy('nombre')
            ->get();

        return view('programas.edit', compact('programa', 'entidades'));
    }

    // Actualizar programa.
    public function update(Request $request, Programa $programa)
    {
        // Validacion de programa.
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'unique:programas,codigo,'.$programa->id],
            'entidad_id' => ['required', 'exists:entidades,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable'],
        ]);

        $validated['activo'] = $request->has('activo');

        // Restriccion de entidad para proyectos relacionados.
        if ((int) $programa->entidad_id !== (int) $validated['entidad_id'] && $programa->proyectos()->exists()) {
            return back()
                ->withErrors(['entidad_id' => 'No puedes cambiar la entidad porque el programa ya tiene proyectos.'])
                ->withInput();
        }

        $programa->update($validated);

        return redirect()->route('programas.index')
            ->with('success', 'Programa actualizado correctamente.');
    }

    // Desactivar programa.
    public function destroy(Programa $programa)
    {
        // Desactivacion para conservar proyectos.
        $programa->update(['activo' => false]);

        return redirect()->route('programas.index')
            ->with('success', 'Programa desactivado correctamente.');
    }
}
