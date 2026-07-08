<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;

class SeguimientoProyectoController extends Controller
{
    // Mostrar seguimiento del proyecto.
    public function show(Proyecto $proyecto)
    {
        $proyecto->load([
            'programa.entidad',

            // Historial de avances.
            'avances' => function ($consulta) {
                $consulta->with(['user', 'evidencias'])
                    ->orderBy('fecha', 'desc')
                    ->orderBy('id', 'desc');
            },

            'ultimoAvance',
        ]);

        // Calculo del avance del proyecto.
        $progresoProyecto = (int) round($proyecto->progreso);

        return view('seguimiento.proyecto_show', compact('proyecto', 'progresoProyecto'));
    }
}
