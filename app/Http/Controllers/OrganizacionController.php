<?php

namespace App\Http\Controllers;

use App\Models\Entidad;

class OrganizacionController extends Controller
{
    // Mostrar organizacion por entidades.
    public function index()
    {
        $entidades = Entidad::where('activo', 1)->with([
            'plans.metas.indicadores.ultimoAvance',
            'programas',
            'proyectos',
        ])
            ->orderBy('id', 'desc')
            ->get();

        // Calculo del resumen por entidad.
        $entidadesConKpi = $entidades->map(function ($e) {
            $planes = $e->plans->where('activo', true);
            $metas = $planes->flatMap(fn ($plan) => $plan->metas->where('activo', true));

            $promedio = $metas->count()
                ? round($metas->avg(fn ($m) => $m->progreso), 0)
                : 0;

            $e->kpi_planes = $planes->count();
            $e->kpi_metas = $metas->count();
            $e->kpi_programas = $e->programas->where('activo', true)->count();
            $e->kpi_proyectos = $e->proyectos->where('activo', true)->count();

            $e->kpi_progreso = max(0, min(100, $promedio));

            return $e;
        });

        return view('seguimiento.organizacion', [
            'entidades' => $entidadesConKpi,
        ]);
    }

    // Mostrar detalle de una entidad.
    public function show(Entidad $entidad)
    {
        $entidad->load([
            'programas',
            'proyectos.programa',
            'proyectos.meta',
            'plans.pdn',
            'plans.metas.indicadores.ultimoAvance',
        ]);

        $metas = $entidad->plans
            ->where('activo', true)
            ->flatMap(fn ($plan) => $plan->metas->where('activo', true));

        // Calculo del avance de la entidad.
        $progresoEntidad = $metas->count()
            ? round($metas->avg(fn ($m) => $m->progreso), 0)
            : 0;

        $progresoEntidad = max(0, min(100, $progresoEntidad));

        return view('seguimiento.organizacion_entidad', compact('entidad', 'metas', 'progresoEntidad'));
    }
}
