<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use App\Models\Indicador;
use App\Models\Meta;
use App\Models\Plan;
use App\Models\Proyecto;
use App\Models\ProyectoAvance;

class DashboardController extends Controller
{
    // Mostrar resumen del sistema.
    public function index()
    {
        // Conteos del dashboard.
        $kpis = [
            'planes_activos' => Plan::where('activo', 1)->count(),
            'metas' => Meta::where('activo', 1)->count(),
            'indicadores' => Indicador::where('activo', 1)->count(),
            'proyectos' => Proyecto::where('activo', 1)->count(),
        ];

        // Calculo del avance de metas.
        $metas = Meta::where('activo', 1)->with(['indicadores' => function ($query) {
            $query->where('activo', 1)->with('ultimoAvance');
        }])->get();

        $metasMedibles = $metas->filter(fn ($meta) => $meta->indicadores->isNotEmpty());
        $progresoInstitucional = $metasMedibles->count()
            ? (int) round($metasMedibles->avg(fn ($meta) => (float) $meta->progreso))
            : 0;

        $progresoInstitucional = max(0, min(100, $progresoInstitucional));
        $metasCompletadas = $metasMedibles->filter(fn ($meta) => $meta->completada)->count();
        $metasEnProgreso = max(0, $metasMedibles->count() - $metasCompletadas);
        $metasPendientes = max(0, $metas->count() - $metasMedibles->count());

        // Distribucion de metas por estado.
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

        // Calculo de alineacion estrategica.
        $totalMetas = $metas->count();
        $metasAlineadas = Meta::where('activo', 1)
            ->whereHas('alineaciones', fn ($query) => $query->where('activo', 1))
            ->count();
        $metasNoAlineadas = max(0, $totalMetas - $metasAlineadas);
        $porcentajeAlineacion = $totalMetas > 0
            ? (int) round(($metasAlineadas / $totalMetas) * 100)
            : 0;

        // Calculo del avance de proyectos.
        $proyectos = Proyecto::where('activo', 1)->with(['ultimoAvance'])->get();
        $progresoProyectos = $proyectos->count()
            ? (int) round($proyectos->avg(fn ($proyecto) => (float) $proyecto->progreso))
            : 0;

        $proyectosCompletados = $proyectos->filter(fn ($proyecto) => (float) $proyecto->progreso >= 100)->count();
        $proyectosEnProgreso = max(0, $proyectos->count() - $proyectosCompletados);

        // Avance de las 3 primeras entidades.
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

        // Ultimos avances de proyectos.
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
