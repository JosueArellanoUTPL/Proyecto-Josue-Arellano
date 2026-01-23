<?php

namespace App\Http\Controllers;

use App\Models\Programa;

/**
 * Controlador de seguimiento de Programa.
 *
 * Se muestra:
 * - ficha del programa
 * - KPIs (total proyectos, activos, inactivos)
 * - progreso promedio del programa (promedio del progreso de sus proyectos)
 *
 * El programa no registra avances directamente.
 * Los avances se registran en Proyectos (ProyectoAvance).
 */
class SeguimientoProgramaController extends Controller
{
    /**
     * Detalle de seguimiento del programa.
     * Se carga entidad y proyectos con su último avance para calcular progreso real.
     */
    public function show(Programa $programa)
    {
        // Se carga entidad y proyectos con último avance (para que $proyecto->progreso funcione)
        $programa->load([
            'entidad',
            'proyectos' => function ($q) {
                $q->with(['ultimoAvance'])
                  ->orderBy('id', 'desc');
            }
        ]);

        // KPIs simples para tarjetas informativas
        $kpiProyectos = $programa->proyectos->count();
        $kpiActivos   = $programa->proyectos->where('activo', 1)->count();
        $kpiInactivos = $kpiProyectos - $kpiActivos;

        // Progreso del programa = promedio del progreso de sus proyectos
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
