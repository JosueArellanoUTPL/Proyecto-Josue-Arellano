<?php

namespace App\Http\Controllers;

use App\Models\Entidad;

class OrganizacionController extends Controller
{
    public function index()
    {
        // Carga entidades con todo lo necesario para calcular KPIs.
        $entidades = Entidad::where('activo', 1)->with([
                'plans.metas.indicadores.ultimoAvance',
                'programas',
                'proyectos'
            ])
            ->orderBy('id', 'desc')
            ->get();

        // Calcula tarjetas de planes, metas, programas, proyectos y progreso.
        $entidadesConKpi = $entidades->map(function ($e) {
            // Todas las metas de todos los planes de esta entidad.
            $planes = $e->plans->where('activo', true);
            $metas = $planes->flatMap(fn ($plan) => $plan->metas->where('activo', true));

            // Promedio de avance basado en metas.
            $promedio = $metas->count()
                ? round($metas->avg(fn ($m) => $m->progreso), 0)
                : 0;

            // Valores que se muestran como tarjetas en la vista.
            $e->kpi_planes = $planes->count();
            $e->kpi_metas = $metas->count();
            $e->kpi_programas = $e->programas->where('activo', true)->count();
            $e->kpi_proyectos = $e->proyectos->where('activo', true)->count();

            // Progreso limitado entre 0 y 100.
            $e->kpi_progreso = max(0, min(100, $promedio));

            return $e;
        });

        return view('seguimiento.organizacion', [
            'entidades' => $entidadesConKpi
        ]);
    }

    public function show(Entidad $entidad)
    {
        // Detalle de una entidad con programas, proyectos, planes y metas.
        $entidad->load([
            'programas',
            'proyectos.programa',
            'proyectos.meta',
            'plans.pdn',
            'plans.metas.indicadores.ultimoAvance'
        ]);

        // Todas las metas de todos los planes de la entidad.
        $metas = $entidad->plans
            ->where('activo', true)
            ->flatMap(fn ($plan) => $plan->metas->where('activo', true));

        // Progreso promedio de la entidad.
        $progresoEntidad = $metas->count()
            ? round($metas->avg(fn ($m) => $m->progreso), 0)
            : 0;

        $progresoEntidad = max(0, min(100, $progresoEntidad));

        return view('seguimiento.organizacion_entidad', compact('entidad', 'metas', 'progresoEntidad'));
    }
}
