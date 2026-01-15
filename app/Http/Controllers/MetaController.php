<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\Plan;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    public function index()
    {
        $metas = Meta::with('plan')->orderBy('id', 'desc')->get();
        return view('metas.index', compact('metas'));
    }

    public function create()
    {
        $plans = Plan::where('activo', true)->orderBy('id', 'desc')->get();
        return view('metas.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:30',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'plan_id' => 'required|exists:plans,id',
            'valor_objetivo' => 'nullable|numeric',
            'unidad' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ]);

        Meta::create($data);

        return redirect()->route('metas.index')
            ->with('success', 'Meta creada correctamente.');
    }

    public function edit(Meta $meta)
    {
        $plans = Plan::where('activo', true)->orderBy('id', 'desc')->get();
        return view('metas.edit', compact('meta', 'plans'));
    }

    public function update(Request $request, Meta $meta)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:30',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'plan_id' => 'required|exists:plans,id',
            'valor_objetivo' => 'nullable|numeric',
            'unidad' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ]);

        $meta->update($data);

        return redirect()->route('metas.index')
            ->with('success', 'Meta actualizada correctamente.');
    }

    public function destroy(Meta $meta)
    {
        $meta->delete();

        return redirect()->route('metas.index')
            ->with('success', 'Meta eliminada correctamente.');
    }
}
