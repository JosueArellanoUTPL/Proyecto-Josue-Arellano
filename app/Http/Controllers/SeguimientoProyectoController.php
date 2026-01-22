<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;

class SeguimientoProyectoController extends Controller
{
    public function show(Proyecto $proyecto)
    {
        $proyecto->load([
            'entidad',
            'programa',
            'avances' => function ($q) {
                $q->with(['user', 'evidencias'])
                  ->orderBy('fecha', 'desc')
                  ->orderBy('id', 'desc');
            },
            'ultimoAvance', // importante
        ]);

        // ✅ progreso real basado en el último avance
        $progresoProyecto = (int) round($proyecto->progreso);

        return view('seguimiento.proyecto_show', compact('proyecto', 'progresoProyecto'));
    }
}
