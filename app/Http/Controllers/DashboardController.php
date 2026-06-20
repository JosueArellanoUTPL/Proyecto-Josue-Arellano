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
            'metas' => Meta::where('activo', 1)->count(),
            'indicadores' => Indicador::where('activo', 1)->count(),
            'alineaciones' => Alineacion::where('activo', 1)->count(),
            'programas' => Programa::where('activo', 1)->count(),
            'proyectos' => Proyecto::where('activo', 1)->count(),
        ];

        // Traigo metas con indicadores para calcular el avance general del dashboard.
        $metas = Meta::where('activo', 1)->with(['indicadores' => function ($query) {
            $query->where('activo', 1)->with('ultimoAvance');
        }])->get();

        // Las metas sin indicadores quedan pendientes y no alteran el promedio.
        $metasMedibles = $metas->filter(fn ($meta) => $meta->indicadores->isNotEmpty());
        $progresoInstitucional = $metasMedibles->count()
            ? (int) round($metasMedibles->avg(fn ($meta) => (float) $meta->progreso))
            : 0;

        // Se asegura que el porcentaje nunca salga de 0 a 100.
        $progresoInstitucional = max(0, min(100, $progresoInstitucional));
        $metasCompletadas = $metasMedibles->filter(fn ($meta) => $meta->completada)->count();
        $metasEnProgreso = max(0, $metasMedibles->count() - $metasCompletadas);
        $metasPendientes = max(0, $metas->count() - $metasMedibles->count());

        // Grafica simple: cantidad y porcentaje de metas por estado.
        $distribucionMetas = collect([
            ['label' => 'Completadas', 'total' => $metasCompletadas, 'color' => 'var(--green)'],
            ['label' => 'En progreso', 'total' => $metasEnProgreso, 'color' => 'var(--orange)'],
            ['label' => 'Pendientes', 'total' => $metasPendientes, 'color' => '#94a3b8'],
        ])->map(function ($estado) use ($metas) {
            $estado['porcentaje'] = $metas->count()
                ? (int) round(($estado['total'] / $metas->count()) * 100)
                : 0;

            return $estado;
        });

        // Datos para la tarjeta de alineacion estrategica.
        $totalMetas = $metas->count();
        $metasAlineadas = Meta::where('activo', 1)
            ->whereHas('alineaciones', fn ($query) => $query->where('activo', 1))
            ->count();
        $metasNoAlineadas = max(0, $totalMetas - $metasAlineadas);
        $porcentajeAlineacion = $totalMetas > 0
            ? (int) round(($metasAlineadas / $totalMetas) * 100)
            : 0;

        // Datos para la dona de proyectos segun el ultimo avance registrado.
        $proyectos = Proyecto::where('activo', 1)->with(['ultimoAvance'])->get();
        $progresoProyectos = $proyectos->count()
            ? (int) round($proyectos->avg(fn ($proyecto) => (float) $proyecto->progreso))
            : 0;

        $proyectosCompletados = $proyectos->filter(fn ($proyecto) => (float) $proyecto->progreso >= 100)->count();
        $proyectosEnProgreso = max(0, $proyectos->count() - $proyectosCompletados);

        // Ranking simple: muestra como maximo 3 entidades.
        $avancePorEntidad = Entidad::where('activo', 1)->with(['plans.metas.indicadores.ultimoAvance'])
            ->orderBy('nombre')
            ->get()
            ->map(function ($entidad) {
                $metasEntidad = $entidad->plans
                    ->where('activo', true)
                    ->flatMap(fn ($plan) => $plan->metas->where('activo', true));
                $metasMedibles = $metasEntidad->filter(fn ($meta) => $meta->indicadores->where('activo', true)->isNotEmpty());

                return [
                    'nombre' => $entidad->nombre,
                    'total_metas' => $metasEntidad->count(),
                    'progreso' => $metasMedibles->count()
                        ? (int) round($metasMedibles->avg(fn ($meta) => (float) $meta->progreso))
                        : 0,
                ];
            })
            ->sortByDesc('progreso')
            ->take(3)
            ->values();

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
            'metasPendientes',
            'distribucionMetas',
            'metasAlineadas',
            'metasNoAlineadas',
            'porcentajeAlineacion',
            'progresoProyectos',
            'proyectosCompletados',
            'proyectosEnProgreso',
            'avancePorEntidad',
            'actividadReciente'
        ));
    }
}
