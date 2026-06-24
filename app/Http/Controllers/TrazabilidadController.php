<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Entidad;
use App\Models\Meta;
use App\Models\ObjetivoEstrategico;
use App\Models\Ods;
use App\Models\Pdn;
use Illuminate\Http\Request;

class TrazabilidadController extends Controller
{
    // Mostrar trazabilidad con filtros.
    public function index(Request $request)
    {
        // Filtros de trazabilidad.
        $entidadId = $request->query('entidad_id');
        $metaId = $request->query('meta_id');
        $odsId = $request->query('ods_id');
        $pdnId = $request->query('pdn_id');
        $objetivoId = $request->query('objetivo_estrategico_id');
        $soloActivas = $request->query('solo_activas', '1');

        $consulta = Alineacion::query()
            ->with([
                'meta.plan.entidad',
                'meta.plan.pdn',
                'ods',
                'objetivoEstrategico',
            ]);

        if ($soloActivas === '1') {
            $consulta->where('activo', 1);
        }

        if (! empty($metaId)) {
            $consulta->where('meta_id', $metaId);
        }

        if (! empty($odsId)) {
            $consulta->where('ods_id', $odsId);
        }
        if (! empty($pdnId)) {
            $consulta->whereHas('meta.plan', fn ($plan) => $plan->where('pdn_id', $pdnId));
        }
        if (! empty($objetivoId)) {
            $consulta->where('objetivo_estrategico_id', $objetivoId);
        }

        if (! empty($entidadId)) {
            $consulta->whereHas('meta.plan', function ($plan) use ($entidadId) {
                $plan->where('entidad_id', $entidadId);
            });
        }

        $alineaciones = $consulta->orderBy('id', 'desc')->get();

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
            'entidadId',
            'metaId',
            'odsId',
            'pdnId',
            'objetivoId',
            'soloActivas'
        ));
    }
}
