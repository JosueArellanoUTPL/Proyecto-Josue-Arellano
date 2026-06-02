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
        // Lista proyectos con entidad y programa para la tabla principal.
        $proyectos = Proyecto::with(['entidad', 'programa'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('proyectos.index', compact('proyectos'));
    }

    public function create()
    {
        // Datos para llenar los select del formulario de proyecto.
        $entidades = Entidad::orderBy('nombre')->get();
        $programas = Programa::orderBy('nombre')->get();

        return view('proyectos.create', compact('entidades', 'programas'));
    }

    public function store(Request $request)
    {
        // Valida datos basicos del proyecto antes de guardar.
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'entidad_id' => ['required', 'exists:entidades,id'],
            'programa_id' => ['required', 'exists:programas,id'],
            'activo' => ['nullable'],
        ]);

        // El checkbox activo llega solo si esta marcado.
        $validated['activo'] = $request->has('activo');

        // Crea el proyecto.
        Proyecto::create($validated);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto creado correctamente.');
    }

    public function edit(Proyecto $proyecto)
    {
        // Datos para editar la entidad/programa asociado al proyecto.
        $entidades = Entidad::orderBy('nombre')->get();
        $programas = Programa::orderBy('nombre')->get();

        return view('proyectos.edit', compact('proyecto', 'entidades', 'programas'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        // Valida antes de actualizar el proyecto.
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'entidad_id' => ['required', 'exists:entidades,id'],
            'programa_id' => ['required', 'exists:programas,id'],
            'activo' => ['nullable'],
        ]);

        // Convierte el checkbox en true/false.
        $validated['activo'] = $request->has('activo');

        // Actualiza el proyecto seleccionado.
        $proyecto->update($validated);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Proyecto $proyecto)
    {
        // Elimina el proyecto desde el listado.
        $proyecto->delete();

        return redirect()->route('proyectos.index')->with('success', 'Proyecto eliminado correctamente.');
    }
}
