<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Entidad;
use App\Models\Meta;
use App\Models\Ods;
use App\Models\Pdn;
use App\Models\ObjetivoEstrategico;
use Illuminate\Http\Request;

/**
 * Controlador de Matriz de Trazabilidad Institucional (Seguimiento)
 *
 * Vista para visualizar la alineación: Meta/Indicador -> ODS/PDN/OE
 * con filtros y KPIs básicos.
 */
class TrazabilidadController extends Controller
{
    public function index(Request $request)
    {
        // Filtros (query params)
        $fEntidad   = $request->query('entidad_id');
        $fMeta      = $request->query('meta_id');
        $fOds       = $request->query('ods_id');
        $fPdn       = $request->query('pdn_id');
        $fObjetivo  = $request->query('objetivo_estrategico_id');
        $fSoloActivas = $request->query('solo_activas', '1'); // por defecto: solo activas

        // Query principal: alineaciones + contexto institucional
        $q = Alineacion::query()
            ->with([
                'meta.plan.entidad',
                'indicador',
                'ods',
                'pdn',
                'objetivoEstrategico'
            ]);

        if ($fSoloActivas === '1') {
            $q->where('activo', 1);
        }

        // Filtro por meta
        if (!empty($fMeta)) {
            $q->where('meta_id', $fMeta);
        }

        // Filtros por instrumentos
        if (!empty($fOds)) {
            $q->where('ods_id', $fOds);
        }
        if (!empty($fPdn)) {
            $q->where('pdn_id', $fPdn);
        }
        if (!empty($fObjetivo)) {
            $q->where('objetivo_estrategico_id', $fObjetivo);
        }

        // Filtro por entidad (se aplica vía relación meta->plan->entidad)
        if (!empty($fEntidad)) {
            $q->whereHas('meta.plan', function ($qq) use ($fEntidad) {
                $qq->where('entidad_id', $fEntidad);
            });
        }

        $alineaciones = $q->orderBy('id', 'desc')->get();

        // KPIs rápidos para la cabecera
        $kpiTotal = $alineaciones->count();
        $kpiConIndicador = $alineaciones->whereNotNull('indicador_id')->count();
        $kpiConODS = $alineaciones->whereNotNull('ods_id')->count();
        $kpiConPDN = $alineaciones->whereNotNull('pdn_id')->count();
        $kpiConOE  = $alineaciones->whereNotNull('objetivo_estrategico_id')->count();

        // Catálogos para filtros (solo activos)
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
            'kpiTotal',
            'kpiConIndicador',
            'kpiConODS',
            'kpiConPDN',
            'kpiConOE',
            'fEntidad',
            'fMeta',
            'fOds',
            'fPdn',
            'fObjetivo',
            'fSoloActivas'
        ));
    }
}
