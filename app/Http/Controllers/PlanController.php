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
        $plans = Plan::with(['pdn', 'entidad'])
            ->orderBy('id', 'desc')
            ->get();

        return view('plans.index', compact('plans'));
    }

    public function create()
    {
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $entidades = Entidad::where('activo', true)->orderBy('id', 'desc')->get();

        return view('plans.create', compact('pdns', 'entidades'));
    }

    public function store(Request $request)
    {
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

        Plan::create($data);

        return redirect()->route('plans.index')
            ->with('success', 'Plan creado correctamente.');
    }

    public function edit(Plan $plan)
    {
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $entidades = Entidad::where('activo', true)->orderBy('id', 'desc')->get();

        return view('plans.edit', compact('plan', 'pdns', 'entidades'));
    }

    public function update(Request $request, Plan $plan)
    {
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

        $plan->update($data);

        return redirect()->route('plans.index')
            ->with('success', 'Plan actualizado correctamente.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return redirect()->route('plans.index')
            ->with('success', 'Plan eliminado correctamente.');
    }
}
