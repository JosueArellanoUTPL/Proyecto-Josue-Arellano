<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Meta;
use App\Models\Indicador;
use App\Models\Alineacion;
use App\Models\Programa;
use App\Models\Proyecto;

class DashboardController extends Controller
{
    public function index()
    {
        // KPI Cards
        $kpis = [
            'planes_activos' => Plan::where('activo', true)->count(),
            'metas' => Meta::count(),
            'indicadores' => Indicador::count(),
            'alineaciones' => Alineacion::count(),
            'programas' => Programa::count(),
            'proyectos' => Proyecto::count(),
        ];

        // Donut: Metas alineadas vs no alineadas
        $metasAlineadas = Alineacion::distinct('meta_id')->count('meta_id');
        $metasTotales = Meta::count();
        $metasNoAlineadas = max(0, $metasTotales - $metasAlineadas);

        // Bar: Indicadores por Plan (contando indicadores a través de Metas)
        $indicadoresPorPlan = Plan::withCount([
            'metas as indicadores_count' => function ($q) {
                $q->join('indicadores', 'indicadores.meta_id', '=', 'metas.id');
            }
        ])
            ->orderBy('id', 'desc')
            ->get(['id', 'codigo', 'nombre']);

        return view('dashboard', compact(
            'kpis',
            'metasAlineadas',
            'metasNoAlineadas',
            'indicadoresPorPlan'
        ));
    }
}
