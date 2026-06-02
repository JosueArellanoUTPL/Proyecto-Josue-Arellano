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
        // Lista planes con su PDN y entidad para mostrar informacion completa.
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
        // Valida que el plan tenga codigo, fechas correctas, PDN y entidad.
        $data = $request->validate([
            'codigo' => 'required|string|max:30',
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
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $entidades = Entidad::where('activo', true)->orderBy('id', 'desc')->get();

        return view('plans.edit', compact('plan', 'pdns', 'entidades'));
    }

    public function update(Request $request, Plan $plan)
    {
        // Misma validacion de crear, pero actualizando el plan existente.
        $data = $request->validate([
            'codigo' => 'required|string|max:30',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'anio_inicio' => 'required|integer',
            'anio_fin' => 'required|integer|gte:anio_inicio',
            'pdn_id' => 'required|exists:pdns,id',
            'entidad_id' => 'required|exists:entidades,id',
            'activo' => 'required|boolean',
        ]);

        // Actualiza el plan seleccionado.
        $plan->update($data);

        return redirect()->route('plans.index')
            ->with('success', 'Plan actualizado correctamente.');
    }

    public function destroy(Plan $plan)
    {
        // Elimina el plan desde el listado.
        $plan->delete();

        return redirect()->route('plans.index')
            ->with('success', 'Plan eliminado correctamente.');
    }
}
