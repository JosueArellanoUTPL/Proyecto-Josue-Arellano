<?php

namespace App\Http\Controllers;

use App\Models\Entidad;

class OrganizacionController extends Controller
{
    public function index()
    {
        // Cargamos: programas, proyectos, planes y metas para poder mostrar conteos y avance
        $entidades = Entidad::with([
            'plans.metas.indicadores.ultimoAvance',
            'programas',
            'proyectos'
        ])->orderBy('id', 'desc')->get();

        // Calculamos KPIs por entidad (en memoria, simple y claro)
        $entidades = $entidades->map(function ($e) {
            $metas = $e->plans->flatMap->metas;
            $promedio = $metas->count() ? round($metas->avg(fn($m) => $m->progreso), 0) : 0;

            $e->kpi_planes = $e->plans->count();
            $e->kpi_metas = $metas->count();
            $e->kpi_programas = $e->programas->count();
            $e->kpi_proyectos = $e->proyectos->count();
            $e->kpi_progreso = max(0, min(100, $promedio));

            return $e;
        });

        return view('seguimiento.organizacion', compact('entidades'));
    }

    public function show(Entidad $entidad)
    {
        $entidad->load([
            'programas',
            'proyectos.programa',
            'plans.pdn',
            'plans.metas.indicadores.ultimoAvance'
        ]);

        // metas de todos sus planes
        $metas = $entidad->plans->flatMap->metas;

        $progresoEntidad = $metas->count() ? round($metas->avg(fn($m) => $m->progreso), 0) : 0;
        $progresoEntidad = max(0, min(100, $progresoEntidad));

        return view('seguimiento.organizacion_entidad', compact('entidad', 'metas', 'progresoEntidad'));
    }
}
