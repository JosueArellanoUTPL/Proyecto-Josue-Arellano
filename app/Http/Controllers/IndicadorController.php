<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use App\Models\Meta;
use Illuminate\Http\Request;

class IndicadorController extends Controller
{
    // Listar indicadores.
    public function index()
    {
        $indicadores = Indicador::with('meta')->orderBy('id', 'desc')->get();

        return view('indicadores.index', compact('indicadores'));
    }

    // Mostrar formulario para crear indicador.
    public function create()
    {
        $metas = Meta::where('activo', true)->orderBy('id', 'desc')->get();

        return view('indicadores.create', compact('metas'));
    }

    // Guardar indicador.
    public function store(Request $request)
    {
        // Validacion de indicador.
        $data = $request->validate([
            'codigo' => 'required|string|max:30|unique:indicadores,codigo',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'meta_id' => 'required|exists:metas,id',
            'linea_base' => 'required|numeric',
            'valor_meta' => 'required|numeric',
            'unidad' => 'required|string|max:50',
            'activo' => 'required|boolean',
        ]);

        Indicador::create($data);

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador creado correctamente.');
    }

    // Mostrar formulario para editar indicador.
    public function edit(Indicador $indicadore)
    {
        $indicador = $indicadore;

        $metas = Meta::where('activo', true)->orWhere('id', $indicador->meta_id)->orderBy('id', 'desc')->get();

        return view('indicadores.edit', compact('indicador', 'metas'));
    }

    // Actualizar indicador.
    public function update(Request $request, Indicador $indicadore)
    {
        $indicador = $indicadore;

        // Validacion de indicador.
        $data = $request->validate([
            'codigo' => 'required|string|max:30|unique:indicadores,codigo,'.$indicadore->id,
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'meta_id' => 'required|exists:metas,id',
            'linea_base' => 'required|numeric',
            'valor_meta' => 'required|numeric',
            'unidad' => 'required|string|max:50',
            'activo' => 'required|boolean',
        ]);

        $indicador->update($data);

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador actualizado correctamente.');
    }

    // Desactivar indicador.
    public function destroy(Indicador $indicadore)
    {
        // Desactivacion para conservar avances.
        $indicador = $indicadore;
        $indicador->update(['activo' => false]);

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador desactivado correctamente.');
    }
}
