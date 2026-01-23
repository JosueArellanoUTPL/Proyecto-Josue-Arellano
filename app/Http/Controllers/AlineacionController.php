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
    /**
     * Lista de alineaciones.
     * Se cargan relaciones para mostrar nombres/códigos sin consultas repetidas en la vista.
     */
    public function index()
    {
        $alineaciones = Alineacion::with(['meta', 'indicador', 'ods', 'pdn', 'objetivoEstrategico'])
            ->orderBy('id', 'desc')
            ->get();

        return view('alineaciones.index', compact('alineaciones'));
    }

    /**
     * Formulario de creación.
     * Se cargan catálogos activos para selección.
     */
    public function create()
    {
        $metas = Meta::where('activo', true)->orderBy('id', 'desc')->get();
        $indicadores = Indicador::where('activo', true)->orderBy('id', 'desc')->get();
        $ods = Ods::where('activo', true)->orderBy('id', 'desc')->get();
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $objetivos = ObjetivoEstrategico::where('activo', true)->orderBy('id', 'desc')->get();

        return view('alineaciones.create', compact('metas', 'indicadores', 'ods', 'pdns', 'objetivos'));
    }

    /**
     * Guarda alineación.
     * Reglas:
     * - meta_id obligatorio
     * - indicador_id opcional, pero si viene debe pertenecer a la meta
     * - al menos uno entre (ods, pdn, objetivo estratégico)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'meta_id' => ['required', 'exists:metas,id'],
            'indicador_id' => ['nullable', 'exists:indicadores,id'],

            'ods_id' => ['nullable', 'exists:ods,id'],
            'pdn_id' => ['nullable', 'exists:pdns,id'],
            'objetivo_estrategico_id' => ['nullable', 'exists:objetivo_estrategicos,id'],

            // Checkbox: si no viene, se considera false
            'activo' => ['nullable'],
        ]);

        // Checkbox coherente con el resto del sistema
        $data['activo'] = $request->has('activo');

        // Se exige al menos un instrumento seleccionado
        if (empty($data['ods_id']) && empty($data['pdn_id']) && empty($data['objetivo_estrategico_id'])) {
            return back()
                ->withErrors(['ods_id' => 'Se requiere seleccionar al menos un instrumento (ODS, PDN o Objetivo Estratégico).'])
                ->withInput();
        }

        // Coherencia: si se selecciona indicador, debe pertenecer a la meta seleccionada
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

        Alineacion::create($data);

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineación creada correctamente.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(Alineacion $alineacion)
    {
        $metas = Meta::where('activo', true)->orderBy('id', 'desc')->get();
        $indicadores = Indicador::where('activo', true)->orderBy('id', 'desc')->get();
        $ods = Ods::where('activo', true)->orderBy('id', 'desc')->get();
        $pdns = Pdn::where('activo', true)->orderBy('id', 'desc')->get();
        $objetivos = ObjetivoEstrategico::where('activo', true)->orderBy('id', 'desc')->get();

        return view('alineaciones.edit', compact('alineacion', 'metas', 'indicadores', 'ods', 'pdns', 'objetivos'));
    }

    /**
     * Actualiza alineación.
     * Se aplican las mismas reglas que en store().
     */
    public function update(Request $request, Alineacion $alineacion)
    {
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
                ->withErrors(['ods_id' => 'Se requiere seleccionar al menos un instrumento (ODS, PDN o Objetivo Estratégico).'])
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

        $alineacion->update($data);

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineación actualizada correctamente.');
    }

    /**
     * Elimina alineación.
     */
    public function destroy(Alineacion $alineacion)
    {
        $alineacion->delete();

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineación eliminada correctamente.');
    }
}
