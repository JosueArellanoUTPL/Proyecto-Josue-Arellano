<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;

class SeguimientoProyectoController extends Controller
{
    public function show(Proyecto $proyecto)
    {
        // Carga datos necesarios para ver proyecto, historial y evidencias.
        $proyecto->load([
            'programa.entidad',
            'meta.plan',

            // Historial completo de avances con usuario y evidencias.
            'avances' => function ($q) {
                $q->with(['user', 'evidencias'])
                  ->orderBy('fecha', 'desc')
                  ->orderBy('id', 'desc');
            },

            // Ultimo avance para calcular el progreso actual.
            'ultimoAvance',
        ]);

        // Progreso actual tomado desde el accessor del modelo Proyecto.
        $progresoProyecto = (int) round($proyecto->progreso);

        return view('seguimiento.proyecto_show', compact('proyecto', 'progresoProyecto'));
    }
}
