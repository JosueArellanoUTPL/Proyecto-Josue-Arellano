<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Meta;
use App\Models\Indicador;
use App\Models\Ods;
use App\Models\Pdn;
use App\Models\ObjetivoEstrategico;
use Illuminate\Http\Request;

class AlineacionController extends Controller
{
    public function index()
    {
        $alineaciones = Alineacion::with(['meta', 'indicador', 'ods', 'pdn', 'objetivoEstrategico'])
            ->orderBy('id', 'desc')
            ->get();

        return view('alineaciones.index', compact('alineaciones'));
    }

    public function create()
    {
        $metas = Meta::where('activo', true)->orderBy('id', 'desc')->get();
        $indicadores = Indicador::where('activo', true)->orderBy('id', 'desc')->get();
        $ods = Ods::where('activo', true)->orderBy('id', 'desc')->get();
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $objetivos = ObjetivoEstrategico::where('activo', true)->orderBy('id', 'desc')->get();

        return view('alineaciones.create', compact('metas', 'indicadores', 'ods', 'pdns', 'objetivos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'meta_id' => 'required|exists:metas,id',
            'indicador_id' => 'nullable|exists:indicadores,id',

            'ods_id' => 'nullable|exists:ods,id',
            'pdn_id' => 'nullable|exists:pdns,id',
            'objetivo_estrategico_id' => 'nullable|exists:objetivo_estrategicos,id',

            // Al menos uno de estos debe venir
            'activo' => 'required|boolean',
        ]);

        // Validación extra: al menos un instrumento seleccionado
        if (empty($data['ods_id']) && empty($data['pdn_id']) && empty($data['objetivo_estrategico_id'])) {
            return back()
                ->withErrors(['ods_id' => 'Debes seleccionar al menos un instrumento (ODS, PND/PDN u Objetivo Estratégico).'])
                ->withInput();
        }

        Alineacion::create($data);

        return redirect()->route('alineaciones.index')
            ->with('success', 'Alineación creada correctamente.');
    }

    public function edit(Alineacion $alineacion)
    {
        $metas = Meta::where('activo', true)->orderBy('id', 'desc')->get();
        $indicadores = Indicador::where('activo', true)->orderBy('id', 'desc')->get();
        $ods = Ods::where('activo', true)->orderBy('id', 'desc')->get();
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $objetivos = ObjetivoEstrategico::where('activo', true)->orderBy('id', 'desc')->get();

        return view('alineaciones.edit', compact('alineacion', 'metas', 'indicadores', 'ods', 'pdns', 'objetivos'));
    }

    public function update(Request $request, Alineacion $alineacion)
    {
        $data = $request->validate([
            'meta_id' => 'required|exists:metas,id',
            'indicador_id' => 'nullable|exists:indicadores,id',

            'ods_id' => 'nullable|exists:ods,id',
            'pdn_id' => 'nullable|exists:pdns,id',
            'objetivo_estrategico_id' => 'nullable|exists:objetivo_estrategicos,id',

            'activo' => 'required|boolean',
        ]);

        if (empty($data['ods_id']) && empty($data['pdn_id']) && empty($data['objetivo_estrategico_id'])) {
            return back()
                ->withErrors(['ods_id' => 'Debes seleccionar al menos un instrumento (ODS, PND/PDN u Objetivo Estratégico).'])
                ->withInput();
        }

        $alineacion->update($data);

        return redirect()->route('alineaciones.index')
            ->with('success', 'Alineación actualizada correctamente.');
    }

    public function destroy(Alineacion $alineacion)
    {
        $alineacion->delete();

        return redirect()->route('alineaciones.index')
            ->with('success', 'Alineación eliminada correctamente.');
    }
}
