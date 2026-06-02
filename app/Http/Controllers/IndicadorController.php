<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use App\Models\Meta;
use Illuminate\Http\Request;

class IndicadorController extends Controller
{
    public function index()
    {
        // Lista indicadores con su meta para mostrar contexto en la tabla.
        $indicadores = Indicador::with('meta')->orderBy('id', 'desc')->get();
        return view('indicadores.index', compact('indicadores'));
    }

    public function create()
    {
        // Solo uso metas activas para crear un indicador nuevo.
        $metas = Meta::where('activo', true)->orderBy('id', 'desc')->get();
        return view('indicadores.create', compact('metas'));
    }

    public function store(Request $request)
    {
        // Valida el formulario antes de guardar el indicador.
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

        // Crea el indicador asociado a una meta.
        Indicador::create($data);

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador creado correctamente.');
    }

    public function edit(Indicador $indicadore)
    {
        // Laravel usa $indicadore por el resource; aqui lo renombro para entenderlo mejor.
        $indicador = $indicadore;

        // Metas disponibles para cambiar la asociacion del indicador.
        $metas = Meta::where('activo', true)->orderBy('id', 'desc')->get();
        return view('indicadores.edit', compact('indicador', 'metas'));
    }

    public function update(Request $request, Indicador $indicadore)
    {
        // Misma idea: renombro la variable para que el codigo sea mas claro.
        $indicador = $indicadore;

        // Valida los datos antes de actualizar.
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

        // Actualiza el indicador seleccionado.
        $indicador->update($data);

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador actualizado correctamente.');
    }

    public function destroy(Indicador $indicadore)
    {
        // Renombro y elimino el indicador seleccionado.
        $indicador = $indicadore;
        $indicador->delete();

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador eliminado correctamente.');
    }
}
