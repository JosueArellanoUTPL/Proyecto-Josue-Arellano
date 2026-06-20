<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use App\Models\Meta;
use App\Models\Programa;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProyectoController extends Controller
{
    // Listar proyectos.
    public function index()
    {
        $proyectos = Proyecto::with(['programa.entidad', 'meta.plan'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('proyectos.index', compact('proyectos'));
    }

    // Mostrar formulario para crear proyecto.
    public function create()
    {
        $entidades = Entidad::where('activo', true)->orderBy('nombre')->get();
        $programas = Programa::with('entidad')->where('activo', true)->orderBy('nombre')->get();
        $metas = Meta::with('plan.entidad')->where('activo', true)->orderBy('nombre')->get();

        return view('proyectos.create', compact('entidades', 'programas', 'metas'));
    }

    // Guardar proyecto.
    public function store(Request $request)
    {
        $validated = $this->validateProject($request);

        // Estado activo.
        $validated['activo'] = $request->has('activo');

        Proyecto::create($validated);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto creado correctamente.');
    }

    // Mostrar formulario para editar proyecto.
    public function edit(Proyecto $proyecto)
    {
        $proyecto->load('programa.entidad');
        $entidades = Entidad::where('activo', true)->orWhere('id', $proyecto->programa?->entidad_id)->orderBy('nombre')->get();
        $programas = Programa::with('entidad')->where('activo', true)->orWhere('id', $proyecto->programa_id)->orderBy('nombre')->get();
        $metas = Meta::with('plan.entidad')->where('activo', true)->orWhere('id', $proyecto->meta_id)->orderBy('nombre')->get();

        return view('proyectos.edit', compact('proyecto', 'entidades', 'programas', 'metas'));
    }

    // Actualizar proyecto.
    public function update(Request $request, Proyecto $proyecto)
    {
        $validated = $this->validateProject($request);

        // Estado activo.
        $validated['activo'] = $request->has('activo');

        $proyecto->update($validated);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto actualizado correctamente.');
    }

    // Desactivar proyecto.
    public function destroy(Proyecto $proyecto)
    {
        // Desactivacion para conservar avances.
        $proyecto->update(['activo' => false]);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto desactivado correctamente.');
    }

    // Validacion de proyecto.
    private function validateProject(Request $request): array
    {
        // Validacion de proyecto.
        $validator = Validator::make($request->all(), [
            'codigo' => ['required', 'string', 'max:30', Rule::unique('proyectos', 'codigo')->ignore($request->route('proyecto'))],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'programa_id' => ['required', 'exists:programas,id'],
            'meta_id' => ['nullable', 'exists:metas,id'],
            'activo' => ['nullable'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $entidadPrograma = Programa::whereKey($request->programa_id)->value('entidad_id');

            // Validacion de entidad entre programa y meta.
            $metaValida = ! $request->filled('meta_id') || Meta::whereKey($request->meta_id)
                ->whereHas('plan', fn ($query) => $query->where('entidad_id', $entidadPrograma))
                ->exists();

            if (! $metaValida) {
                $validator->errors()->add('meta_id', 'La meta debe pertenecer a un plan de la entidad seleccionada.');
            }
        });

        return $validator->validate();
    }
}
