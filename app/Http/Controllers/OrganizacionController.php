<?php

namespace App\Http\Controllers;

use App\Models\Entidad;

class OrganizacionController extends Controller
{
    // Mostrar organizacion por entidades.
    public function index()
    {
        $entidades = Entidad::where('activo', 1)->with([
            'planes.metas.indicadores.ultimoAvance',
            'programas',
            'proyectos',
        ])
            ->orderBy('id', 'desc')
            ->get();

        // Calculo del resumen por entidad.
        $entidadesConKpi = $entidades->map(function ($entidad) {
            $planes = $entidad->planes->where('activo', true);
            $metas = $planes->flatMap(fn ($plan) => $plan->metas->where('activo', true));

            $promedio = $metas->count()
                ? round($metas->avg(fn ($meta) => $meta->progreso), 0)
                : 0;

            $entidad->kpi_planes = $planes->count();
            $entidad->kpi_metas = $metas->count();
            $entidad->kpi_programas = $entidad->programas->where('activo', true)->count();
            $entidad->kpi_proyectos = $entidad->proyectos->where('activo', true)->count();

            $entidad->kpi_progreso = max(0, min(100, $promedio));

            return $entidad;
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
            'planes.pdn',
            'planes.metas.indicadores.ultimoAvance',
        ]);

        $metas = $entidad->planes
            ->where('activo', true)
            ->flatMap(fn ($plan) => $plan->metas->where('activo', true));

        // Calculo del avance de la entidad.
        $progresoEntidad = $metas->count()
            ? round($metas->avg(fn ($meta) => $meta->progreso), 0)
            : 0;

        $progresoEntidad = max(0, min(100, $progresoEntidad));

        return view('seguimiento.organizacion_entidad', compact('entidad', 'metas', 'progresoEntidad'));
    }
}
