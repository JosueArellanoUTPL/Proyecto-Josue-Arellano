<?php

namespace App\Http\Controllers;

use App\Models\Programa;

class SeguimientoProgramaController extends Controller
{
    public function show(Programa $programa)
    {
        // Carga entidad y proyectos para calcular avance del programa.
        $programa->load([
            'entidad',
            'proyectos' => function ($q) {
                $q->with(['ultimoAvance'])
                  ->orderBy('id', 'desc');
            }
        ]);

        // KPIs simples para tarjetas informativas.
        $kpiProyectos = $programa->proyectos->count();
        $kpiActivos = $programa->proyectos->where('activo', 1)->count();
        $kpiInactivos = $kpiProyectos - $kpiActivos;

        // Progreso del programa = promedio del progreso de sus proyectos.
        if ($kpiProyectos > 0) {
            $avg = $programa->proyectos->avg(function ($pry) {
                return (float) ($pry->progreso ?? 0);
            });

            $progresoPrograma = (int) round($avg);
        } else {
            $progresoPrograma = 0;
        }

        return view('seguimiento.programa_show', compact(
            'programa',
            'kpiProyectos',
            'kpiActivos',
            'kpiInactivos',
            'progresoPrograma'
        ));
    }
}
