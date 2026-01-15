<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use App\Models\Meta;
use Illuminate\Http\Request;

class IndicadorController extends Controller
{
    public function index()
    {
        $indicadores = Indicador::with('meta')->orderBy('id', 'desc')->get();
        return view('indicadores.index', compact('indicadores'));
    }

    public function create()
    {
        $metas = Meta::where('activo', true)->orderBy('id', 'desc')->get();
        return view('indicadores.create', compact('metas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:30',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'meta_id' => 'required|exists:metas,id',
            'linea_base' => 'nullable|numeric',
            'valor_meta' => 'nullable|numeric',
            'unidad' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ]);

        Indicador::create($data);

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador creado correctamente.');
    }

    public function edit(Indicador $indicadore)
    {
        // Nota: Laravel puede generar el nombre de variable raro por el resource binding.
        // Lo normal es renombrarlo a $indicador; lo hacemos aquí:
        $indicador = $indicadore;

        $metas = Meta::where('activo', true)->orderBy('id', 'desc')->get();
        return view('indicadores.edit', compact('indicador', 'metas'));
    }

    public function update(Request $request, Indicador $indicadore)
    {
        $indicador = $indicadore;

        $data = $request->validate([
            'codigo' => 'required|string|max:30',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'meta_id' => 'required|exists:metas,id',
            'linea_base' => 'nullable|numeric',
            'valor_meta' => 'nullable|numeric',
            'unidad' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ]);

        $indicador->update($data);

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador actualizado correctamente.');
    }

    public function destroy(Indicador $indicadore)
    {
        $indicador = $indicadore;
        $indicador->delete();

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador eliminado correctamente.');
    }
}
