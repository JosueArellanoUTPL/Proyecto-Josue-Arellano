<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Entidad;
use App\Models\Programa;
use App\Models\Meta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProyectoController extends Controller
{
    public function index()
    {
        // La entidad se carga mediante el programa del proyecto.
        $proyectos = Proyecto::with(['programa.entidad', 'meta.plan'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('proyectos.index', compact('proyectos'));
    }

    public function create()
    {
        // Datos para llenar los select del formulario de proyecto.
        $entidades = Entidad::where('activo', true)->orderBy('nombre')->get();
        $programas = Programa::with('entidad')->where('activo', true)->orderBy('nombre')->get();
        $metas = Meta::with('plan.entidad')->where('activo', true)->orderBy('nombre')->get();

        return view('proyectos.create', compact('entidades', 'programas', 'metas'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProject($request);

        // El checkbox activo llega solo si esta marcado.
        $validated['activo'] = $request->has('activo');

        // Crea el proyecto.
        Proyecto::create($validated);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto creado correctamente.');
    }

    public function edit(Proyecto $proyecto)
    {
        // La entidad solo ayuda a filtrar programas y metas en el formulario.
        $proyecto->load('programa.entidad');
        $entidades = Entidad::where('activo', true)->orWhere('id', $proyecto->programa?->entidad_id)->orderBy('nombre')->get();
        $programas = Programa::with('entidad')->where('activo', true)->orWhere('id', $proyecto->programa_id)->orderBy('nombre')->get();
        $metas = Meta::with('plan.entidad')->where('activo', true)->orWhere('id', $proyecto->meta_id)->orderBy('nombre')->get();

        return view('proyectos.edit', compact('proyecto', 'entidades', 'programas', 'metas'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $validated = $this->validateProject($request);

        // Convierte el checkbox en true/false.
        $validated['activo'] = $request->has('activo');

        // Actualiza el proyecto seleccionado.
        $proyecto->update($validated);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Proyecto $proyecto)
    {
        // Se desactiva para conservar avances, evidencias y trazabilidad.
        $proyecto->update(['activo' => false]);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto desactivado correctamente.');
    }

    private function validateProject(Request $request): array
    {
        // Primero revisa que los campos existan y tengan el formato correcto.
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

            // La meta debe estar dentro de un plan de la misma entidad.
            $metaValida = !$request->filled('meta_id') || Meta::whereKey($request->meta_id)
                ->whereHas('plan', fn ($query) => $query->where('entidad_id', $entidadPrograma))
                ->exists();

            if (!$metaValida) {
                $validator->errors()->add('meta_id', 'La meta debe pertenecer a un plan de la entidad seleccionada.');
            }
        });

        return $validator->validate();
    }
}
