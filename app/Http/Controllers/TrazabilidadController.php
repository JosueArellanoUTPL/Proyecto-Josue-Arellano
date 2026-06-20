<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Entidad;
use App\Models\Meta;
use App\Models\Ods;
use App\Models\Pdn;
use App\Models\ObjetivoEstrategico;
use Illuminate\Http\Request;

class TrazabilidadController extends Controller
{
    public function index(Request $request)
    {
        // Filtros que llegan por GET desde la pantalla de trazabilidad.
        $fEntidad = $request->query('entidad_id');
        $fMeta = $request->query('meta_id');
        $fOds = $request->query('ods_id');
        $fPdn = $request->query('pdn_id');
        $fObjetivo = $request->query('objetivo_estrategico_id');
        $fSoloActivas = $request->query('solo_activas', '1');

        // Consulta principal con relaciones para mostrar nombres y codigos.
        $q = Alineacion::query()
            ->with([
                'meta.plan.entidad',
                'meta.plan.pdn',
                'ods',
                'objetivoEstrategico'
            ]);

        if ($fSoloActivas === '1') {
            $q->where('activo', 1);
        }

        // Filtros directos de la matriz.
        if (!empty($fMeta)) {
            $q->where('meta_id', $fMeta);
        }

        if (!empty($fOds)) {
            $q->where('ods_id', $fOds);
        }
        if (!empty($fPdn)) {
            $q->whereHas('meta.plan', fn ($plan) => $plan->where('pdn_id', $fPdn));
        }
        if (!empty($fObjetivo)) {
            $q->where('objetivo_estrategico_id', $fObjetivo);
        }

        // Filtro por entidad usando la relacion meta -> plan -> entidad.
        if (!empty($fEntidad)) {
            $q->whereHas('meta.plan', function ($qq) use ($fEntidad) {
                $qq->where('entidad_id', $fEntidad);
            });
        }

        $alineaciones = $q->orderBy('id', 'desc')->get();

        // Catalogos para llenar los filtros.
        $entidades = Entidad::where('activo', 1)->orderBy('nombre')->get();
        $metas = Meta::where('activo', 1)->orderBy('codigo')->get();
        $ods = Ods::where('activo', 1)->orderBy('codigo')->get();
        $pdns = Pdn::where('activo', 1)->orderBy('codigo')->get();
        $objetivos = ObjetivoEstrategico::where('activo', 1)->orderBy('nombre')->get();

        return view('seguimiento.trazabilidad', compact(
            'alineaciones',
            'entidades',
            'metas',
            'ods',
            'pdns',
            'objetivos',
            'fEntidad',
            'fMeta',
            'fOds',
            'fPdn',
            'fObjetivo',
            'fSoloActivas'
        ));
    }
}
