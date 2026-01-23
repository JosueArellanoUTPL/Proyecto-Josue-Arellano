<?php

namespace App\Http\Controllers;

use App\Models\Entidad;

/**
 * Controlador de Seguimiento Organizacional
 *
 * Muestra el avance institucional por Entidad:
 * - KPIs (planes, metas, programas, proyectos)
 * - progreso promedio basado en metas e indicadores
 *
 * No realiza CRUD, solo consulta y visualización.
 */
class OrganizacionController extends Controller
{
    /**
     * Vista "Organización (Entidades)"
     *
     * Se carga toda la estructura necesaria para calcular KPIs:
     * Entidad -> Planes -> Metas -> Indicadores -> Último avance
     * Además se cargan programas y proyectos para conteos.
     */
    public function index()
    {
        // Carga de relaciones para evitar consultas repetidas en Blade
        $entidades = Entidad::with([
                'plans.metas.indicadores.ultimoAvance',
                'programas',
                'proyectos'
            ])
            ->orderBy('id', 'desc')
            ->get();

        /**
         * Se calculan KPIs por entidad en memoria:
         * - cantidad de planes, metas, programas y proyectos
         * - progreso promedio basado en metas (cada meta ya calcula su progreso)
         */
        $entidadesConKpi = $entidades->map(function ($e) {
            // Todas las metas de todos los planes de esta entidad
            $metas = $e->plans->flatMap->metas;

            // Progreso promedio (0 a 100)
            $promedio = $metas->count()
                ? round($metas->avg(fn ($m) => $m->progreso), 0)
                : 0;

            // KPIs (valores que se muestran como tarjetas en la vista)
            $e->kpi_planes = $e->plans->count();
            $e->kpi_metas = $metas->count();
            $e->kpi_programas = $e->programas->count();
            $e->kpi_proyectos = $e->proyectos->count();

            // Se limita el KPI de progreso entre 0 y 100
            $e->kpi_progreso = max(0, min(100, $promedio));

            return $e;
        });

        return view('seguimiento.organizacion', [
            'entidades' => $entidadesConKpi
        ]);
    }

    /**
     * Vista "Detalle de Entidad"
     *
     * Se muestra:
     * - programas y proyectos asociados
     * - planes, metas y su avance
     * - progreso promedio de la entidad (basado en metas)
     */
    public function show(Entidad $entidad)
    {
        $entidad->load([
            'programas',
            'proyectos.programa',
            'plans.pdn',
            'plans.metas.indicadores.ultimoAvance'
        ]);

        // Todas las metas de todos los planes de la entidad
        $metas = $entidad->plans->flatMap->metas;

        // Progreso promedio de la entidad (0 a 100)
        $progresoEntidad = $metas->count()
            ? round($metas->avg(fn ($m) => $m->progreso), 0)
            : 0;

        $progresoEntidad = max(0, min(100, $progresoEntidad));

        return view('seguimiento.organizacion_entidad', compact('entidad', 'metas', 'progresoEntidad'));
    }
}
