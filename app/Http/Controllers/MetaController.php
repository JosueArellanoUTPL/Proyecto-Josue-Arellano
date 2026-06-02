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
        $metas = Meta::with('plan')->orderBy('id', 'desc')->get();
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
            'codigo' => 'required|string|max:30',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'plan_id' => 'required|exists:plans,id',
            'valor_objetivo' => 'nullable|numeric',
            'unidad' => 'nullable|string|max:50',
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
        $plans = Plan::where('activo', true)->orderBy('id', 'desc')->get();
        return view('metas.edit', compact('meta', 'plans'));
    }

    public function update(Request $request, Meta $meta)
    {
        // Valida los datos antes de actualizar la meta.
        $data = $request->validate([
            'codigo' => 'required|string|max:30',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'plan_id' => 'required|exists:plans,id',
            'valor_objetivo' => 'nullable|numeric',
            'unidad' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ]);

        // Actualiza la meta seleccionada.
        $meta->update($data);

        return redirect()->route('metas.index')
            ->with('success', 'Meta actualizada correctamente.');
    }

    public function destroy(Meta $meta)
    {
        // Elimina la meta seleccionada desde el listado.
        $meta->delete();

        return redirect()->route('metas.index')
            ->with('success', 'Meta eliminada correctamente.');
    }
}
