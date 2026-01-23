<?php

namespace App\Http\Controllers;

use App\Models\Meta;

/**
 * Controlador de Seguimiento de Metas
 *
 * Se encarga de mostrar:
 * - listado general de metas con su progreso
 * - detalle de una meta con sus indicadores y avances
 *
 * No realiza operaciones CRUD.
 * Toda la lógica se basa en lectura y visualización de datos reales.
 */
class SeguimientoController extends Controller
{
    /**
     * Vista principal de seguimiento de metas.
     *
     * Se cargan:
     * - la meta
     * - su plan asociado
     * - los indicadores con su último avance
     *
     * Esto permite calcular y mostrar barras de progreso
     * sin recalcular lógica en la vista.
     */
    public function index()
    {
        $metas = Meta::with([
                'plan',
                'indicadores.ultimoAvance'
            ])
            ->orderBy('id', 'desc')
            ->get();

        return view('seguimiento.metas', compact('metas'));
    }

    /**
     * Vista detalle de una meta.
     *
     * Se cargan:
     * - el plan de la meta
     * - los indicadores
     * - el último avance de cada indicador
     * - el historial completo de avances
     *
     * Esta información se usa para:
     * - mostrar progreso individual
     * - permitir registrar nuevos avances
     * - visualizar evidencias
     */
    public function show(Meta $meta)
    {
        $meta->load([
            'plan',
            'indicadores.ultimoAvance',
            'indicadores.avances'
        ]);

        return view('seguimiento.meta_show', compact('meta'));
    }
}
