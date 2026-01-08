<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Entidad;
use App\Models\Programa;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::with(['entidad', 'programa'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('proyectos.index', compact('proyectos'));
    }

    public function create()
    {
        $entidades = Entidad::orderBy('nombre')->get();
        $programas = Programa::orderBy('nombre')->get();

        return view('proyectos.create', compact('entidades', 'programas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'entidad_id' => ['required', 'exists:entidades,id'],
            'programa_id' => ['required', 'exists:programas,id'],
            'activo' => ['nullable'],
        ]);

        $validated['activo'] = $request->has('activo');

        Proyecto::create($validated);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto creado correctamente.');
    }

    public function edit(Proyecto $proyecto)
    {
        $entidades = Entidad::orderBy('nombre')->get();
        $programas = Programa::orderBy('nombre')->get();

        return view('proyectos.edit', compact('proyecto', 'entidades', 'programas'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'entidad_id' => ['required', 'exists:entidades,id'],
            'programa_id' => ['required', 'exists:programas,id'],
            'activo' => ['nullable'],
        ]);

        $validated['activo'] = $request->has('activo');

        $proyecto->update($validated);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Proyecto $proyecto)
    {
        $proyecto->delete();

        return redirect()->route('proyectos.index')->with('success', 'Proyecto eliminado correctamente.');
    }
}
