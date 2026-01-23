<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;

/**
 * Controlador de Seguimiento de Proyecto
 *
 * Se muestra una ficha de seguimiento con:
 * - datos del proyecto (entidad y programa)
 * - progreso actual (basado en el último avance)
 * - historial de avances con evidencias en miniatura
 */
class SeguimientoProyectoController extends Controller
{
    /**
     * Pantalla principal de seguimiento del proyecto.
     *
     * Se cargan relaciones necesarias:
     * - entidad y programa para contexto
     * - avances con usuario y evidencias para historial
     * - ultimoAvance para calcular progreso actual
     */
    public function show(Proyecto $proyecto)
    {
        $proyecto->load([
            'entidad',
            'programa',

            // Historial completo de avances con sus evidencias y el usuario que registró
            'avances' => function ($q) {
                $q->with(['user', 'evidencias'])
                  ->orderBy('fecha', 'desc')
                  ->orderBy('id', 'desc');
            },

            // Último avance del proyecto (sirve para el progreso actual)
            'ultimoAvance',
        ]);

        // Progreso actual del proyecto: se toma del accessor $proyecto->progreso
        $progresoProyecto = (int) round($proyecto->progreso);

        return view('seguimiento.proyecto_show', compact('proyecto', 'progresoProyecto'));
    }
}
