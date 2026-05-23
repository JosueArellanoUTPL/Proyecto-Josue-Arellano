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
    /**
     * Menú principal del módulo de reportes.
     * No calcula datos pesados; solo muestra accesos a reportes disponibles.
     */
    public function index()
    {
        return view('reportes.index');
    }

    /**
     * Reporte general del estado institucional.
     * Resume totales y avances promedio de metas/proyectos.
     */
    public function institucional()
    {
        $metas = Meta::with('indicadores.ultimoAvance')->get();
        $proyectos = Proyecto::with('ultimoAvance')->get();

        $progresoMetas = $metas->count()
            ? round($metas->avg(fn ($meta) => (float) $meta->progreso), 2)
            : 0;

        $progresoProyectos = $proyectos->count()
            ? round($proyectos->avg(fn ($proyecto) => (float) $proyecto->progreso), 2)
            : 0;

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

        $entidades = Entidad::with([
                'plans.metas.indicadores.ultimoAvance',
                'programas',
                'proyectos.ultimoAvance',
            ])
            ->orderBy('nombre')
            ->get();

        return view('reportes.institucional', compact('kpis', 'entidades'));
    }

    /**
     * Reporte de metas con filtros simples.
     * Permite revisar avance por entidad y estado de cumplimiento.
     */
    public function metas(Request $request)
    {
        $query = Meta::with(['plan.entidad', 'indicadores.ultimoAvance'])
            ->orderBy('id', 'desc');

        if ($request->filled('entidad_id')) {
            $query->whereHas('plan', function ($planQuery) use ($request) {
                $planQuery->where('entidad_id', $request->entidad_id);
            });
        }

        $metas = $query->get();

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

    /**
     * Reporte de proyectos con avance actual y número de evidencias.
     */
    public function proyectos(Request $request)
    {
        $query = Proyecto::with([
                'entidad',
                'programa',
                'ultimoAvance',
                'avances.evidencias',
            ])
            ->orderBy('id', 'desc');

        if ($request->filled('entidad_id')) {
            $query->where('entidad_id', $request->entidad_id);
        }

        $proyectos = $query->get();
        $entidades = Entidad::orderBy('nombre')->get();

        return view('reportes.proyectos', compact('proyectos', 'entidades'));
    }

    /**
     * Reporte de trazabilidad estratégica.
     * Muestra relaciones Meta/Indicador con ODS, PDN y Objetivos Estratégicos.
     */
    public function trazabilidad()
    {
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
