<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\Plan;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    public function index()
    {
        // Lista metas con su plan para mostrar la tabla principal.
        $metas = Meta::with('plan')->withCount('indicadores')->orderBy('id', 'desc')->get();
        return view('metas.index', compact('metas'));
    }

    public function create()
    {
        // Solo cargo planes activos para asociar la nueva meta.
        $plans = Plan::where('activo', true)->orderBy('id', 'desc')->get();
        return view('metas.create', compact('plans'));
    }

    public function store(Request $request)
    {
        // Valida los datos que vienen del formulario de crear meta.
        $data = $request->validate([
            'codigo' => 'required|string|max:30|unique:metas,codigo',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'plan_id' => 'required|exists:plans,id',
            'activo' => 'required|boolean',
        ]);

        // Aqui se crea la meta en la base de datos.
        Meta::create($data);

        return redirect()->route('metas.index')
            ->with('success', 'Meta creada correctamente.');
    }

    public function edit(Meta $meta)
    {
        // Cargo planes activos para el select de edicion.
        $plans = Plan::where('activo', true)->orWhere('id', $meta->plan_id)->orderBy('id', 'desc')->get();
        return view('metas.edit', compact('meta', 'plans'));
    }

    public function update(Request $request, Meta $meta)
    {
        // Valida los datos antes de actualizar la meta.
        $data = $request->validate([
            'codigo' => 'required|string|max:30|unique:metas,codigo,'.$meta->id,
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'plan_id' => 'required|exists:plans,id',
            'activo' => 'required|boolean',
        ]);

        // Una meta con proyectos solo puede cambiar a otro plan de la misma entidad.
        $nuevoPlan = Plan::findOrFail($data['plan_id']);
        $entidadActual = $meta->plan?->entidad_id;
        if ($meta->proyectos()->exists() && (int) $entidadActual !== (int) $nuevoPlan->entidad_id) {
            return back()
                ->withErrors(['plan_id' => 'El nuevo plan debe pertenecer a la misma entidad porque la meta ya tiene proyectos.'])
                ->withInput();
        }

        // Actualiza la meta seleccionada.
        $meta->update($data);

        return redirect()->route('metas.index')
            ->with('success', 'Meta actualizada correctamente.');
    }

    public function destroy(Meta $meta)
    {
        // Se desactiva para conservar indicadores, proyectos y alineaciones.
        $meta->update(['activo' => false]);

        return redirect()->route('metas.index')
            ->with('success', 'Meta desactivada correctamente.');
    }
}
