<?php

namespace App\Http\Controllers;

use App\Models\Programa;

class SeguimientoProgramaController extends Controller
{
    // Mostrar seguimiento del programa.
    public function show(Programa $programa)
    {
        $programa->load([
            'entidad',
            'proyectos' => function ($q) {
                $q->with(['ultimoAvance'])
                    ->orderBy('id', 'desc');
            },
        ]);

        // Conteo de proyectos.
        $kpiProyectos = $programa->proyectos->count();
        $kpiActivos = $programa->proyectos->where('activo', 1)->count();
        $kpiInactivos = $kpiProyectos - $kpiActivos;

        // Calculo del avance del programa.
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
