<?php

namespace App\Http\Controllers;

use App\Models\Programa;

class SeguimientoProgramaController extends Controller
{
    public function show(Programa $programa)
    {
        $programa->load([
            'entidad',
            'proyectos' => function ($q) {
                $q->with(['ultimoAvance'])->orderBy('id', 'desc'); // cargar último avance
            }
        ]);

        $kpiProyectos = $programa->proyectos->count();
        $kpiActivos   = $programa->proyectos->where('activo', 1)->count();
        $kpiInactivos = $kpiProyectos - $kpiActivos;

        // ✅ progreso real: promedio de progreso de los proyectos
        if ($kpiProyectos > 0) {
            $avg = $programa->proyectos->avg(function ($pry) {
                return (float) ($pry->progreso ?? 0); // usa accessor getProgresoAttribute
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
