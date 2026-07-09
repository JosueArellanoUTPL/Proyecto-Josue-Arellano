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
        $data = $this->validarIndicador($request);

        $data['linea_base'] = (int) $data['linea_base'];
        $data['valor_meta'] = (int) $data['valor_meta'];

        Indicador::create($data);

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador creado correctamente.');
    }

    // Mostrar formulario para editar indicador.
    public function edit(Indicador $indicador)
    {
        $metas = Meta::where('activo', true)->orWhere('id', $indicador->meta_id)->orderBy('id', 'desc')->get();

        return view('indicadores.edit', compact('indicador', 'metas'));
    }

    // Actualizar indicador.
    public function update(Request $request, Indicador $indicador)
    {
        $data = $this->validarIndicador($request, $indicador->id);

        $data['linea_base'] = (int) $data['linea_base'];
        $data['valor_meta'] = (int) $data['valor_meta'];

        $indicador->update($data);

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador actualizado correctamente.');
    }

    // Desactivar indicador.
    public function destroy(Indicador $indicador)
    {
        // Desactivacion para conservar avances.
        $indicador->update(['activo' => false]);

        return redirect()->route('indicadores.index')
            ->with('success', 'Indicador desactivado correctamente.');
    }

    // Validacion de indicador.
    private function validarIndicador(Request $request, ?int $indicadorId = null): array
    {
        return $request->validate([
            'codigo' => 'required|string|max:30|unique:indicadores,codigo'.($indicadorId ? ','.$indicadorId : ''),
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'meta_id' => 'required|exists:metas,id',
            'linea_base' => 'required|integer',
            'valor_meta' => 'required|integer',
            'unidad' => 'required|string|max:50',
            'activo' => 'required|boolean',
        ]);
    }
}
