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
            'proyectos' => function ($consulta) {
                $consulta->with(['ultimoAvance'])
                    ->orderBy('id', 'desc');
            },
        ]);

        // Conteo de proyectos.
        $kpiProyectos = $programa->proyectos->count();
        $kpiActivos = $programa->proyectos->where('activo', 1)->count();
        $kpiInactivos = $kpiProyectos - $kpiActivos;

        // Calculo del avance del programa.
        if ($kpiProyectos > 0) {
            $promedio = $programa->proyectos->avg(function ($proyecto) {
                return (float) ($proyecto->progreso ?? 0);
            });

            $progresoPrograma = (int) round($promedio);
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
