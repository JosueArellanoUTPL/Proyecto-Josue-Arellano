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
        $planes = Plan::where('activo', true)->orderBy('id', 'desc')->get();

        return view('metas.create', compact('planes'));
    }

    // Guardar meta.
    public function store(Request $request)
    {
        $data = $this->validarMeta($request);

        Meta::create($data);

        return redirect()->route('metas.index')
            ->with('success', 'Meta creada correctamente.');
    }

    // Mostrar formulario para editar meta.
    public function edit(Meta $meta)
    {
        $planes = Plan::where('activo', true)->orWhere('id', $meta->plan_id)->orderBy('id', 'desc')->get();

        return view('metas.edit', compact('meta', 'planes'));
    }

    // Actualizar meta.
    public function update(Request $request, Meta $meta)
    {
        $data = $this->validarMeta($request, $meta->id);

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

    // Validacion de meta.
    private function validarMeta(Request $request, ?int $metaId = null): array
    {
        return $request->validate([
            'codigo' => 'required|string|max:30|unique:metas,codigo'.($metaId ? ','.$metaId : ''),
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'plan_id' => 'required|exists:planes,id',
            'activo' => 'required|boolean',
        ]);
    }
}
