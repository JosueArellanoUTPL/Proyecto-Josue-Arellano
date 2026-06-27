<?php

namespace App\Http\Controllers;

use App\Models\ObjetivoEstrategico;
use Illuminate\Http\Request;

class ObjetivoEstrategicoController extends Controller
{
    // Listar objetivos estrategicos.
    public function index()
    {
        $objetivos = ObjetivoEstrategico::orderBy('id', 'desc')->get();

        return view('objetivos-estrategicos.index', compact('objetivos'));
    }

    // Mostrar formulario para crear objetivo.
    public function create()
    {
        return view('objetivos-estrategicos.create');
    }

    // Guardar objetivo estrategico.
    public function store(Request $request)
    {
        $data = $this->validarObjetivo($request);

        ObjetivoEstrategico::create($data);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estrategico creado correctamente.');
    }

    // Mostrar formulario para editar objetivo.
    public function edit(ObjetivoEstrategico $objetivo)
    {
        return view('objetivos-estrategicos.edit', compact('objetivo'));
    }

    // Actualizar objetivo estrategico.
    public function update(Request $request, ObjetivoEstrategico $objetivo)
    {
        $data = $this->validarObjetivo($request, $objetivo->id);

        $objetivo->update($data);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estrategico actualizado correctamente.');
    }

    // Validacion de objetivo estrategico.
    private function validarObjetivo(Request $request, ?int $objetivoId = null): array
    {
        return $request->validate([
            'codigo' => 'required|string|max:20|unique:objetivos_estrategicos,codigo'.($objetivoId ? ','.$objetivoId : ''),
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);
    }

    // Desactivar objetivo estrategico.
    public function destroy(ObjetivoEstrategico $objetivo)
    {
        // Desactivacion para conservar alineaciones.
        $objetivo->update(['activo' => false]);

        return redirect()
            ->route('objetivos-estrategicos.index')
            ->with('success', 'Objetivo estratégico desactivado correctamente.');
    }
}
