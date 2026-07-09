<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Meta;
use App\Models\Proyecto;

class ReporteController extends Controller
{
    // Mostrar menu de reportes.
    public function index()
    {
        return view('reportes.index');
    }

    // Generar reporte de metas.
    public function metas()
    {
        // Listado general de metas para reporte.
        $metas = Meta::where('activo', 1)
            ->with([
                'plan.entidad',
                'indicadores' => fn ($query) => $query->where('activo', 1)->with('ultimoAvance'),
            ])
            ->orderBy('id', 'desc')
            ->get();

        return view('reportes.metas', compact('metas'));
    }

    // Generar reporte de proyectos.
    public function proyectos()
    {
        // Listado general de proyectos para reporte.
        $proyectos = Proyecto::where('activo', 1)->with([
            'programa.entidad',
            'ultimoAvance',
            'avances.evidencias',
        ])
            ->orderBy('id', 'desc')
            ->get();

        return view('reportes.proyectos', compact('proyectos'));
    }

    // Generar reporte de trazabilidad.
    public function trazabilidad()
    {
        $alineaciones = Alineacion::with([
            'meta.plan.entidad',
            'meta.plan.pdn',
            'ods',
            'objetivoEstrategico',
        ])
            ->orderBy('id', 'desc')
            ->get();

        return view('reportes.trazabilidad', compact('alineaciones'));
    }
}
