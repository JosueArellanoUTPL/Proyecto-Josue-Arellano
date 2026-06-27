<?php

namespace App\Http\Controllers;

use App\Models\Ods;
use Illuminate\Http\Request;

class OdsController extends Controller
{
    // Listar ODS.
    public function index()
    {
        $objetivosOds = Ods::orderBy('id', 'desc')->get();

        return view('ods.index', compact('objetivosOds'));
    }

    // Mostrar formulario para crear ODS.
    public function create()
    {
        return view('ods.create');
    }

    // Guardar ODS.
    public function store(Request $request)
    {
        $data = $this->validarOds($request);

        Ods::create($data);

        return redirect()->route('ods.index')
            ->with('success', 'ODS creado correctamente.');
    }

    // Mostrar formulario para editar ODS.
    public function edit(Ods $ods)
    {
        return view('ods.edit', compact('ods'));
    }

    // Actualizar ODS.
    public function update(Request $request, Ods $ods)
    {
        $data = $this->validarOds($request, $ods->id);

        $ods->update($data);

        return redirect()->route('ods.index')
            ->with('success', 'ODS actualizado correctamente.');
    }

    // Desactivar ODS.
    public function destroy(Ods $ods)
    {
        // Desactivacion para conservar alineaciones.
        $ods->update(['activo' => false]);

        return redirect()->route('ods.index')
            ->with('success', 'ODS desactivado correctamente.');
    }

    // Validacion de ODS.
    private function validarOds(Request $request, ?int $odsId = null): array
    {
        return $request->validate([
            'codigo' => 'required|string|max:10|unique:ods,codigo'.($odsId ? ','.$odsId : ''),
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);
    }
}
