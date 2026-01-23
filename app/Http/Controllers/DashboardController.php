<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Meta;
use App\Models\Indicador;
use App\Models\Alineacion;
use App\Models\Programa;
use App\Models\Proyecto;
use App\Models\ProyectoAvance;

class DashboardController extends Controller
{
    public function index()
    {
        /* ===========================
         * KPIs PRINCIPALES
         * =========================== */
        $kpis = [
            'planes_activos' => Plan::where('activo', 1)->count(),
            'metas'          => Meta::count(),
            'indicadores'    => Indicador::count(),
            'alineaciones'   => Alineacion::count(),
            'programas'      => Programa::count(),
            'proyectos'      => Proyecto::count(),
        ];

        /* ===========================
         * PROGRESO INSTITUCIONAL
         * (progreso es accessor en Meta, no columna DB)
         * =========================== */
        $metas = Meta::with(['indicadores.ultimoAvance'])->get();

        $progresoInstitucional = $metas->count()
            ? (int) round($metas->avg(fn ($m) => (float) $m->progreso))
            : 0;

        $progresoInstitucional = max(0, min(100, $progresoInstitucional));

        /* ===========================
         * ESTADO DE ALINEACIÓN
         * =========================== */
        $metasAlineadas = Meta::whereHas('alineaciones')->count();
        $metasNoAlineadas = max(0, Meta::count() - $metasAlineadas);

        /* ===========================
         * ACTIVIDAD RECIENTE
         * (se devuelve como MODELOS, no arrays)
         * =========================== */
        $actividadReciente = ProyectoAvance::with(['proyecto'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'kpis',
            'progresoInstitucional',
            'metasAlineadas',
            'metasNoAlineadas',
            'actividadReciente'
        ));
    }
}
