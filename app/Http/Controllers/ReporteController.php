<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Entidad;
use App\Models\Meta;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    // Mostrar menu de reportes.
    public function index()
    {
        return view('reportes.index');
    }

    // Generar reporte de metas.
    public function metas(Request $request)
    {
        // Validacion de filtros de metas.
        $request->validate([
            'entidad_id' => ['nullable', 'exists:entidades,id'],
            'estado' => ['nullable', 'in:completadas,en_progreso,pendientes'],
        ]);

        $query = Meta::where('activo', 1)
            ->with([
                'plan.entidad',
                'indicadores' => fn ($query) => $query->where('activo', 1)->with('ultimoAvance'),
            ])
            ->withCount(['proyectos' => fn ($query) => $query->where('activo', 1)])
            ->orderBy('id', 'desc');

        if ($request->filled('entidad_id')) {
            $query->whereHas('plan', function ($planQuery) use ($request) {
                $planQuery->where('entidad_id', $request->entidad_id);
            });
        }

        $metas = $query->get();

        // Filtro por estado calculado.
        if ($request->filled('estado')) {
            $metas = $metas->filter(function ($meta) use ($request) {
                return match ($request->estado) {
                    'completadas' => $meta->completada,
                    'pendientes' => $meta->indicadores->isEmpty(),
                    default => $meta->indicadores->isNotEmpty() && ! $meta->completada,
                };
            });
        }

        $entidades = Entidad::orderBy('nombre')->get();

        return view('reportes.metas', compact('metas', 'entidades'));
    }

    // Generar reporte de proyectos.
    public function proyectos(Request $request)
    {
        // Validacion del filtro de proyectos.
        $request->validate([
            'entidad_id' => ['nullable', 'exists:entidades,id'],
        ]);

        $query = Proyecto::where('activo', 1)->with([
            'programa.entidad',
            'meta.plan',
            'ultimoAvance',
            'avances.evidencias',
        ])
            ->orderBy('id', 'desc');

        if ($request->filled('entidad_id')) {
            $query->whereHas('programa', fn ($programa) => $programa->where('entidad_id', $request->entidad_id));
        }

        $proyectos = $query->get();
        $entidades = Entidad::orderBy('nombre')->get();

        return view('reportes.proyectos', compact('proyectos', 'entidades'));
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
