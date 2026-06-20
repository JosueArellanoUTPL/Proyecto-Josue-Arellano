<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\Plan;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    // Listar metas.
    public function index()
    {
        $metas = Meta::with('plan')->withCount('indicadores')->orderBy('id', 'desc')->get();

        return view('metas.index', compact('metas'));
    }

    // Mostrar formulario para crear meta.
    public function create()
    {
        $plans = Plan::where('activo', true)->orderBy('id', 'desc')->get();

        return view('metas.create', compact('plans'));
    }

    // Guardar meta.
    public function store(Request $request)
    {
        // Validacion de meta.
        $data = $request->validate([
            'codigo' => 'required|string|max:30|unique:metas,codigo',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'plan_id' => 'required|exists:plans,id',
            'activo' => 'required|boolean',
        ]);

        Meta::create($data);

        return redirect()->route('metas.index')
            ->with('success', 'Meta creada correctamente.');
    }

    // Mostrar formulario para editar meta.
    public function edit(Meta $meta)
    {
        $plans = Plan::where('activo', true)->orWhere('id', $meta->plan_id)->orderBy('id', 'desc')->get();

        return view('metas.edit', compact('meta', 'plans'));
    }

    // Actualizar meta.
    public function update(Request $request, Meta $meta)
    {
        // Validacion de meta.
        $data = $request->validate([
            'codigo' => 'required|string|max:30|unique:metas,codigo,'.$meta->id,
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'plan_id' => 'required|exists:plans,id',
            'activo' => 'required|boolean',
        ]);

        // Restriccion de entidad para proyectos relacionados.
        $nuevoPlan = Plan::findOrFail($data['plan_id']);
        $entidadActual = $meta->plan?->entidad_id;
        if ($meta->proyectos()->exists() && (int) $entidadActual !== (int) $nuevoPlan->entidad_id) {
            return back()
                ->withErrors(['plan_id' => 'El nuevo plan debe pertenecer a la misma entidad porque la meta ya tiene proyectos.'])
                ->withInput();
        }

        $meta->update($data);

        return redirect()->route('metas.index')
            ->with('success', 'Meta actualizada correctamente.');
    }

    // Desactivar meta.
    public function destroy(Meta $meta)
    {
        // Desactivacion para conservar relaciones.
        $meta->update(['activo' => false]);

        return redirect()->route('metas.index')
            ->with('success', 'Meta desactivada correctamente.');
    }
}
