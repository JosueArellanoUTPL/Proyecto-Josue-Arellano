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
        // Tarjetas pequenas de arriba: solo cuentan registros principales.
        $kpis = [
            'planes_activos' => Plan::where('activo', 1)->count(),
            'metas' => Meta::count(),
            'indicadores' => Indicador::count(),
            'alineaciones' => Alineacion::count(),
            'programas' => Programa::count(),
            'proyectos' => Proyecto::count(),
        ];

        // Traigo metas con indicadores para calcular el avance general del dashboard.
        $metas = Meta::with(['indicadores.ultimoAvance'])->get();

        // Este porcentaje alimenta la dona de "Avance institucional".
        $progresoInstitucional = $metas->count()
            ? (int) round($metas->avg(fn ($meta) => (float) $meta->progreso))
            : 0;

        // Se asegura que el porcentaje nunca salga de 0 a 100.
        $progresoInstitucional = max(0, min(100, $progresoInstitucional));
        $metasCompletadas = $metas->filter(fn ($meta) => $meta->completada)->count();
        $metasEnProgreso = max(0, $metas->count() - $metasCompletadas);

        // Datos para la tarjeta de alineacion estrategica.
        $totalMetas = Meta::count();
        $metasAlineadas = Meta::whereHas('alineaciones')->count();
        $metasNoAlineadas = max(0, $totalMetas - $metasAlineadas);
        $porcentajeAlineacion = $totalMetas > 0
            ? (int) round(($metasAlineadas / $totalMetas) * 100)
            : 0;

        // Datos para la dona de proyectos segun el ultimo avance registrado.
        $proyectos = Proyecto::with(['ultimoAvance'])->get();
        $progresoProyectos = $proyectos->count()
            ? (int) round($proyectos->avg(fn ($proyecto) => (float) $proyecto->progreso))
            : 0;

        $proyectosCompletados = $proyectos->filter(fn ($proyecto) => (float) $proyecto->progreso >= 100)->count();
        $proyectosEnProgreso = max(0, $proyectos->count() - $proyectosCompletados);

        // Ranking simple: muestra las 5 entidades con mejor avance promedio.
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

        // Barras del dashboard: cantidad de avances de proyectos en los ultimos 6 meses.
        $inicio = now()->subMonths(5)->startOfMonth();
        $avancesPorMes = ProyectoAvance::whereDate('fecha', '>=', $inicio)
            ->get()
            ->groupBy(fn ($avance) => $avance->fecha->format('Y-m'))
            ->map->count();

        // Labels en espanol para que la grafica no muestre Jan, Feb, etc.
        $meses = [
            1 => 'ene',
            2 => 'feb',
            3 => 'mar',
            4 => 'abr',
            5 => 'may',
            6 => 'jun',
            7 => 'jul',
            8 => 'ago',
            9 => 'sep',
            10 => 'oct',
            11 => 'nov',
            12 => 'dic',
        ];

        // Aqui se arma cada barra mensual con su etiqueta y total.
        $actividadMensual = collect(range(0, 5))->map(function ($i) use ($inicio, $avancesPorMes, $meses) {
            $mes = (clone $inicio)->addMonths($i);
            $key = $mes->format('Y-m');

            return [
                'label' => $meses[(int) $mes->format('n')],
                'total' => $avancesPorMes->get($key, 0),
            ];
        });

        // Evita division por cero al calcular la altura de las barras en la vista.
        $maxActividadMensual = max(1, $actividadMensual->max('total'));

        // Tarjetas finales: ultimos avances registrados para ver actividad reciente.
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
