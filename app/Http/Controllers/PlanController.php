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
        $planes = Plan::with(['pdn', 'entidad'])
            ->orderBy('id', 'desc')
            ->get();

        return view('planes.index', compact('planes'));
    }

    // Mostrar formulario para crear plan.
    public function create()
    {
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $entidades = Entidad::where('activo', true)->orderBy('id', 'desc')->get();

        return view('planes.create', compact('pdns', 'entidades'));
    }

    // Guardar plan.
    public function store(Request $request)
    {
        $data = $this->validarPlan($request);

        Plan::create($data);

        return redirect()->route('planes.index')
            ->with('success', 'Plan creado correctamente.');
    }

    // Mostrar formulario para editar plan.
    public function edit(Plan $plan)
    {
        $pdns = Pdn::where('activo', true)->orWhere('id', $plan->pdn_id)->orderBy('id', 'desc')->get();
        $entidades = Entidad::where('activo', true)->orWhere('id', $plan->entidad_id)->orderBy('id', 'desc')->get();

        return view('planes.edit', compact('plan', 'pdns', 'entidades'));
    }

    // Actualizar plan.
    public function update(Request $request, Plan $plan)
    {
        $data = $this->validarPlan($request, $plan->id);

        $plan->update($data);

        return redirect()->route('planes.index')
            ->with('success', 'Plan actualizado correctamente.');
    }

    // Desactivar plan.
    public function destroy(Plan $plan)
    {
        // Desactivacion para conservar relaciones.
        $plan->update(['activo' => false]);

        return redirect()->route('planes.index')
            ->with('success', 'Plan desactivado correctamente.');
    }

    // Validacion de plan.
    private function validarPlan(Request $request, ?int $planId = null): array
    {
        return $request->validate([
            'codigo' => 'required|string|max:30|unique:planes,codigo'.($planId ? ','.$planId : ''),
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'anio_inicio' => 'required|integer',
            'anio_fin' => 'required|integer|gte:anio_inicio',
            'pdn_id' => 'required|exists:pdns,id',
            'entidad_id' => 'required|exists:entidades,id',
            'activo' => 'required|boolean',
        ]);
    }
}
