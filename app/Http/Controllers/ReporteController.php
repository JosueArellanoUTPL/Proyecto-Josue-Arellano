<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Entidad;
use App\Models\Indicador;
use App\Models\Meta;
use App\Models\Plan;
use App\Models\Programa;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        // Pantalla inicial del modulo: solo muestra accesos a los reportes.
        return view('reportes.index');
    }

    public function institucional()
    {
        // Datos generales para el reporte institucional.
        $metas = Meta::with('indicadores.ultimoAvance')->get();
        $proyectos = Proyecto::with('ultimoAvance')->get();

        // Promedios que se muestran como porcentajes principales.
        $progresoMetas = $metas->count()
            ? round($metas->avg(fn ($meta) => (float) $meta->progreso), 2)
            : 0;

        $progresoProyectos = $proyectos->count()
            ? round($proyectos->avg(fn ($proyecto) => (float) $proyecto->progreso), 2)
            : 0;

        // Numeros pequenos tipo KPI del reporte.
        $kpis = [
            'entidades' => Entidad::count(),
            'programas' => Programa::count(),
            'proyectos' => Proyecto::count(),
            'planes' => Plan::count(),
            'metas' => Meta::count(),
            'indicadores' => Indicador::count(),
            'alineaciones' => Alineacion::count(),
            'progreso_metas' => $progresoMetas,
            'progreso_proyectos' => $progresoProyectos,
        ];

        // Se carga todo lo necesario para la tabla de resumen por entidad.
        $entidades = Entidad::with([
                'plans.metas.indicadores.ultimoAvance',
                'programas',
                'proyectos.ultimoAvance',
            ])
            ->orderBy('nombre')
            ->get();

        return view('reportes.institucional', compact('kpis', 'entidades'));
    }

    public function metas(Request $request)
    {
        // Consulta base: metas con su plan, entidad e indicadores.
        $query = Meta::with(['plan.entidad', 'indicadores.ultimoAvance'])
            ->orderBy('id', 'desc');

        // Filtro por entidad, usando la entidad del plan.
        if ($request->filled('entidad_id')) {
            $query->whereHas('plan', function ($planQuery) use ($request) {
                $planQuery->where('entidad_id', $request->entidad_id);
            });
        }

        $metas = $query->get();

        // Este filtro se hace en coleccion porque "completada" es un atributo calculado.
        if ($request->filled('estado')) {
            $metas = $metas->filter(function ($meta) use ($request) {
                return $request->estado === 'completadas'
                    ? $meta->completada
                    : !$meta->completada;
            });
        }

        $entidades = Entidad::orderBy('nombre')->get();

        return view('reportes.metas', compact('metas', 'entidades'));
    }

    public function proyectos(Request $request)
    {
        // Consulta base: proyectos con entidad, programa, ultimo avance y evidencias.
        $query = Proyecto::with([
                'entidad',
                'programa',
                'ultimoAvance',
                'avances.evidencias',
            ])
            ->orderBy('id', 'desc');

        // Filtro simple por entidad.
        if ($request->filled('entidad_id')) {
            $query->where('entidad_id', $request->entidad_id);
        }

        $proyectos = $query->get();
        $entidades = Entidad::orderBy('nombre')->get();

        return view('reportes.proyectos', compact('proyectos', 'entidades'));
    }

    public function trazabilidad()
    {
        // Reporte de relaciones: meta, indicador, ODS, PDN y objetivo estrategico.
        $alineaciones = Alineacion::with([
                'meta.plan.entidad',
                'indicador',
                'ods',
                'pdn',
                'objetivoEstrategico',
            ])
            ->orderBy('id', 'desc')
            ->get();

        return view('reportes.trazabilidad', compact('alineaciones'));
    }
}
