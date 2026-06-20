<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use App\Models\Pdn;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    // Listar planes.
    public function index()
    {
        $plans = Plan::with(['pdn', 'entidad'])
            ->orderBy('id', 'desc')
            ->get();

        return view('plans.index', compact('plans'));
    }

    // Mostrar formulario para crear plan.
    public function create()
    {
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $entidades = Entidad::where('activo', true)->orderBy('id', 'desc')->get();

        return view('plans.create', compact('pdns', 'entidades'));
    }

    // Guardar plan.
    public function store(Request $request)
    {
        // Validacion de plan.
        $data = $request->validate([
            'codigo' => 'required|string|max:30|unique:plans,codigo',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'anio_inicio' => 'required|integer',
            'anio_fin' => 'required|integer|gte:anio_inicio',
            'pdn_id' => 'required|exists:pdns,id',
            'entidad_id' => 'required|exists:entidades,id',
            'activo' => 'required|boolean',
        ]);

        Plan::create($data);

        return redirect()->route('plans.index')
            ->with('success', 'Plan creado correctamente.');
    }

    // Mostrar formulario para editar plan.
    public function edit(Plan $plan)
    {
        $pdns = Pdn::where('activo', true)->orWhere('id', $plan->pdn_id)->orderBy('id', 'desc')->get();
        $entidades = Entidad::where('activo', true)->orWhere('id', $plan->entidad_id)->orderBy('id', 'desc')->get();

        return view('plans.edit', compact('plan', 'pdns', 'entidades'));
    }

    // Actualizar plan.
    public function update(Request $request, Plan $plan)
    {
        // Validacion de plan.
        $data = $request->validate([
            'codigo' => 'required|string|max:30|unique:plans,codigo,'.$plan->id,
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'anio_inicio' => 'required|integer',
            'anio_fin' => 'required|integer|gte:anio_inicio',
            'pdn_id' => 'required|exists:pdns,id',
            'entidad_id' => 'required|exists:entidades,id',
            'activo' => 'required|boolean',
        ]);

        // Restriccion de entidad para proyectos relacionados.
        $tieneProyectos = $plan->metas()->whereHas('proyectos')->exists();
        if ((int) $plan->entidad_id !== (int) $data['entidad_id'] && $tieneProyectos) {
            return back()
                ->withErrors(['entidad_id' => 'No puedes cambiar la entidad porque este plan ya tiene proyectos relacionados.'])
                ->withInput();
        }

        $plan->update($data);

        return redirect()->route('plans.index')
            ->with('success', 'Plan actualizado correctamente.');
    }

    // Desactivar plan.
    public function destroy(Plan $plan)
    {
        // Desactivacion para conservar relaciones.
        $plan->update(['activo' => false]);

        return redirect()->route('plans.index')
            ->with('success', 'Plan desactivado correctamente.');
    }
}
