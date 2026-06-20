<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Pdn;
use App\Models\Entidad;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        // Lista planes con su PND y entidad para mostrar información completa.
        $plans = Plan::with(['pdn', 'entidad'])
            ->orderBy('id', 'desc')
            ->get();

        return view('plans.index', compact('plans'));
    }

    public function create()
    {
        // Catalogos activos para llenar los select del formulario.
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $entidades = Entidad::where('activo', true)->orderBy('id', 'desc')->get();

        return view('plans.create', compact('pdns', 'entidades'));
    }

    public function store(Request $request)
    {
        // Valida que el plan tenga código, fechas correctas, PND y entidad.
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

        // Guarda el plan.
        Plan::create($data);

        return redirect()->route('plans.index')
            ->with('success', 'Plan creado correctamente.');
    }

    public function edit(Plan $plan)
    {
        // Datos para los select al editar el plan.
        $pdns = Pdn::where('activo', true)->orWhere('id', $plan->pdn_id)->orderBy('id', 'desc')->get();
        $entidades = Entidad::where('activo', true)->orWhere('id', $plan->entidad_id)->orderBy('id', 'desc')->get();

        return view('plans.edit', compact('plan', 'pdns', 'entidades'));
    }

    public function update(Request $request, Plan $plan)
    {
        // Misma validacion de crear, pero actualizando el plan existente.
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

        // Evita que las metas y proyectos del plan queden asociados a otra entidad.
        $tieneProyectos = $plan->metas()->whereHas('proyectos')->exists();
        if ((int) $plan->entidad_id !== (int) $data['entidad_id'] && $tieneProyectos) {
            return back()
                ->withErrors(['entidad_id' => 'No puedes cambiar la entidad porque este plan ya tiene proyectos relacionados.'])
                ->withInput();
        }

        // Actualiza el plan seleccionado.
        $plan->update($data);

        return redirect()->route('plans.index')
            ->with('success', 'Plan actualizado correctamente.');
    }

    public function destroy(Plan $plan)
    {
        // Se desactiva para no borrar metas, indicadores y avances en cascada.
        $plan->update(['activo' => false]);

        return redirect()->route('plans.index')
            ->with('success', 'Plan desactivado correctamente.');
    }
}
