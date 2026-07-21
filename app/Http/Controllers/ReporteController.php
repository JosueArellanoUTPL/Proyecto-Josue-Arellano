<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\ActividadOperativa;
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

    public function poa()
    {
        $actividades = ActividadOperativa::where('activo', true)
            ->with(['plan.pdn', 'proyecto.programa', 'objetivoEstrategico', 'indicador'])
            ->orderByDesc('id')
            ->get();

        return view('reportes.poa', compact('actividades'));
    }

    public function poaCsv()
    {
        $actividades = ActividadOperativa::where('activo', true)
            ->with(['plan.pdn', 'proyecto.programa', 'objetivoEstrategico', 'indicador'])
            ->orderBy('id')
            ->get();

        return response()->streamDownload(function () use ($actividades) {
            $archivo = fopen('php://output', 'w');
            fwrite($archivo, "\xEF\xBB\xBF");
            fwrite($archivo, "sep=;\r\n");
            fputcsv($archivo, ['Código', 'Actividad', 'Plan', 'PND', 'Proyecto', 'Programa', 'Objetivo estratégico', 'Indicador', 'Responsable', 'Año', 'Inicio', 'Fin', 'Prioridad', 'Meta anual (%)', 'Avance (%)', 'Presupuesto', 'Estado'], ';');

            foreach ($actividades as $actividad) {
                fputcsv($archivo, [
                    $actividad->codigo, $actividad->nombre, $actividad->plan->nombre,
                    $actividad->plan->pdn?->nombre, $actividad->proyecto->nombre,
                    $actividad->proyecto->programa?->nombre, $actividad->objetivoEstrategico->nombre,
                    $actividad->indicador->nombre, $actividad->responsable, $actividad->anio,
                    $actividad->fecha_inicio?->format('d/m/Y'), $actividad->fecha_fin?->format('d/m/Y'), $actividad->prioridad,
                    $actividad->meta_anual, $actividad->avance, $actividad->presupuesto, ActividadOperativa::ESTADOS[$actividad->estado],
                ], ';');
            }

            fclose($archivo);
        }, 'reporte-poa.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
