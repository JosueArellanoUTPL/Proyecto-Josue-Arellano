<?php

namespace App\Http\Controllers;

use App\Models\Pdn;
use Illuminate\Http\Request;

class PdnController extends Controller
{
    // Listar PDN.
    public function index()
    {
        $planesNacionales = Pdn::orderBy('id', 'desc')->get();

        return view('pdn.index', compact('planesNacionales'));
    }

    // Mostrar formulario para crear PDN.
    public function create()
    {
        return view('pdn.create');
    }

    // Guardar PDN.
    public function store(Request $request)
    {
        $data = $this->validarPdn($request);

        Pdn::create($data);

        return redirect()->route('pdn.index')
            ->with('success', 'PND creado correctamente.');
    }

    // Mostrar formulario para editar PDN.
    public function edit(Pdn $pdn)
    {
        return view('pdn.edit', compact('pdn'));
    }

    // Actualizar PDN.
    public function update(Request $request, Pdn $pdn)
    {
        $data = $this->validarPdn($request, $pdn->id);

        $pdn->update($data);

        return redirect()->route('pdn.index')
            ->with('success', 'PND actualizado correctamente.');
    }

    // Desactivar PDN.
    public function destroy(Pdn $pdn)
    {
        // Desactivacion para conservar planes.
        $pdn->update(['activo' => false]);

        return redirect()->route('pdn.index')
            ->with('success', 'PND desactivado correctamente.');
    }

    // Validacion de PND.
    private function validarPdn(Request $request, ?int $pdnId = null): array
    {
        return $request->validate([
            'codigo' => 'required|string|max:20|unique:pdns,codigo'.($pdnId ? ','.$pdnId : ''),
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);
    }
}
