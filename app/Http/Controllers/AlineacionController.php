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
        // Lista alineaciones con sus relaciones para no consultar de mas en la vista.
        $alineaciones = Alineacion::with(['meta', 'indicador', 'ods', 'pdn', 'objetivoEstrategico'])
            ->orderBy('id', 'desc')
            ->get();

        return view('alineaciones.index', compact('alineaciones'));
    }

    public function create()
    {
        // Catalogos activos para armar una alineacion estrategica.
        $metas = Meta::where('activo', true)->orderBy('id', 'desc')->get();
        $indicadores = Indicador::where('activo', true)->orderBy('id', 'desc')->get();
        $ods = Ods::where('activo', true)->orderBy('id', 'desc')->get();
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $objetivos = ObjetivoEstrategico::where('activo', true)->orderBy('id', 'desc')->get();

        return view('alineaciones.create', compact('metas', 'indicadores', 'ods', 'pdns', 'objetivos'));
    }

    public function store(Request $request)
    {
        // Valida meta, indicador opcional e instrumentos estrategicos.
        $data = $request->validate([
            'meta_id' => ['required', 'exists:metas,id'],
            'indicador_id' => ['nullable', 'exists:indicadores,id'],

            'ods_id' => ['nullable', 'exists:ods,id'],
            'pdn_id' => ['nullable', 'exists:pdns,id'],
            'objetivo_estrategico_id' => ['nullable', 'exists:objetivo_estrategicos,id'],

            'activo' => ['nullable'],
        ]);

        // Checkbox activo convertido a true/false.
        $data['activo'] = $request->has('activo');

        // Debe existir al menos un instrumento: ODS, PDN u objetivo.
        if (empty($data['ods_id']) && empty($data['pdn_id']) && empty($data['objetivo_estrategico_id'])) {
            return back()
                ->withErrors(['ods_id' => 'Se requiere seleccionar al menos un instrumento (ODS, PDN o Objetivo Estrategico).'])
                ->withInput();
        }

        // Si escoge indicador, reviso que ese indicador pertenezca a la meta.
        if (!empty($data['indicador_id'])) {
            $ok = Indicador::where('id', $data['indicador_id'])
                ->where('meta_id', $data['meta_id'])
                ->exists();

            if (!$ok) {
                return back()
                    ->withErrors(['indicador_id' => 'El indicador seleccionado no pertenece a la meta seleccionada.'])
                    ->withInput();
            }
        }

        // Guarda la alineacion estrategica.
        Alineacion::create($data);

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineacion creada correctamente.');
    }

    public function edit(Alineacion $alineacion)
    {
        // Cargo los mismos catalogos para editar una alineacion.
        $metas = Meta::where('activo', true)->orderBy('id', 'desc')->get();
        $indicadores = Indicador::where('activo', true)->orderBy('id', 'desc')->get();
        $ods = Ods::where('activo', true)->orderBy('id', 'desc')->get();
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $objetivos = ObjetivoEstrategico::where('activo', true)->orderBy('id', 'desc')->get();

        return view('alineaciones.edit', compact('alineacion', 'metas', 'indicadores', 'ods', 'pdns', 'objetivos'));
    }

    public function update(Request $request, Alineacion $alineacion)
    {
        // Mismas reglas de crear, pero para actualizar.
        $data = $request->validate([
            'meta_id' => ['required', 'exists:metas,id'],
            'indicador_id' => ['nullable', 'exists:indicadores,id'],

            'ods_id' => ['nullable', 'exists:ods,id'],
            'pdn_id' => ['nullable', 'exists:pdns,id'],
            'objetivo_estrategico_id' => ['nullable', 'exists:objetivo_estrategicos,id'],

            'activo' => ['nullable'],
        ]);

        $data['activo'] = $request->has('activo');

        if (empty($data['ods_id']) && empty($data['pdn_id']) && empty($data['objetivo_estrategico_id'])) {
            return back()
                ->withErrors(['ods_id' => 'Se requiere seleccionar al menos un instrumento (ODS, PDN o Objetivo Estrategico).'])
                ->withInput();
        }

        if (!empty($data['indicador_id'])) {
            $ok = Indicador::where('id', $data['indicador_id'])
                ->where('meta_id', $data['meta_id'])
                ->exists();

            if (!$ok) {
                return back()
                    ->withErrors(['indicador_id' => 'El indicador seleccionado no pertenece a la meta seleccionada.'])
                    ->withInput();
            }
        }

        // Actualiza la alineacion seleccionada.
        $alineacion->update($data);

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineacion actualizada correctamente.');
    }

    public function destroy(Alineacion $alineacion)
    {
        // Elimina la alineacion desde la tabla.
        $alineacion->delete();

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineacion eliminada correctamente.');
    }
}
