<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Entidad;
use App\Models\Indicador;
use App\Models\Meta;
use App\Models\Plan;
use App\Models\Programa;
use App\Models\Proyecto;
use App\Models\ProyectoAvance;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs principales del sistema.
        $kpis = [
            'planes_activos' => Plan::where('activo', 1)->count(),
            'metas' => Meta::count(),
            'indicadores' => Indicador::count(),
            'alineaciones' => Alineacion::count(),
            'programas' => Programa::count(),
            'proyectos' => Proyecto::count(),
        ];

        // Progreso institucional calculado desde metas e indicadores.
        $metas = Meta::with(['indicadores.ultimoAvance'])->get();

        $progresoInstitucional = $metas->count()
            ? (int) round($metas->avg(fn ($meta) => (float) $meta->progreso))
            : 0;

        $progresoInstitucional = max(0, min(100, $progresoInstitucional));
        $metasCompletadas = $metas->filter(fn ($meta) => $meta->completada)->count();
        $metasEnProgreso = max(0, $metas->count() - $metasCompletadas);

        // Trazabilidad estratégica: metas alineadas vs no alineadas.
        $totalMetas = Meta::count();
        $metasAlineadas = Meta::whereHas('alineaciones')->count();
        $metasNoAlineadas = max(0, $totalMetas - $metasAlineadas);
        $porcentajeAlineacion = $totalMetas > 0
            ? (int) round(($metasAlineadas / $totalMetas) * 100)
            : 0;

        // Progreso de proyectos según su último avance registrado.
        $proyectos = Proyecto::with(['ultimoAvance'])->get();
        $progresoProyectos = $proyectos->count()
            ? (int) round($proyectos->avg(fn ($proyecto) => (float) $proyecto->progreso))
            : 0;

        $proyectosCompletados = $proyectos->filter(fn ($proyecto) => (float) $proyecto->progreso >= 100)->count();
        $proyectosEnProgreso = max(0, $proyectos->count() - $proyectosCompletados);

        // Ranking simple de avance por entidad.
        $avancePorEntidad = Entidad::with(['plans.metas.indicadores.ultimoAvance'])
            ->orderBy('nombre')
            ->get()
            ->map(function ($entidad) {
                $metasEntidad = $entidad->plans->flatMap->metas;

                return [
                    'nombre' => $entidad->nombre,
                    'total_metas' => $metasEntidad->count(),
                    'progreso' => $metasEntidad->count()
                        ? (int) round($metasEntidad->avg(fn ($meta) => (float) $meta->progreso))
                        : 0,
                ];
            })
            ->sortByDesc('progreso')
            ->take(5)
            ->values();

        // Conteo de avances de proyectos en los últimos 6 meses.
        $inicio = now()->subMonths(5)->startOfMonth();
        $avancesPorMes = ProyectoAvance::whereDate('fecha', '>=', $inicio)
            ->get()
            ->groupBy(fn ($avance) => $avance->fecha->format('Y-m'))
            ->map->count();

        $actividadMensual = collect(range(0, 5))->map(function ($i) use ($inicio, $avancesPorMes) {
            $mes = (clone $inicio)->addMonths($i);
            $key = $mes->format('Y-m');

            return [
                'label' => $mes->format('M'),
                'total' => $avancesPorMes->get($key, 0),
            ];
        });

        $maxActividadMensual = max(1, $actividadMensual->max('total'));

        // Últimos avances para actividad reciente.
        $actividadReciente = ProyectoAvance::with(['proyecto'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'kpis',
            'progresoInstitucional',
            'metasCompletadas',
            'metasEnProgreso',
            'metasAlineadas',
            'metasNoAlineadas',
            'porcentajeAlineacion',
            'progresoProyectos',
            'proyectosCompletados',
            'proyectosEnProgreso',
            'avancePorEntidad',
            'actividadMensual',
            'maxActividadMensual',
            'actividadReciente'
        ));
    }
}
