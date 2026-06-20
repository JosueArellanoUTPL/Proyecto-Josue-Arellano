<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Entidad;
use App\Models\Meta;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        // Pantalla inicial con los accesos a cada reporte.
        return view('reportes.index');
    }

    public function metas(Request $request)
    {
        // Valida solamente los filtros visibles del reporte.
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

        // Filtra las metas usando la entidad que pertenece al plan.
        if ($request->filled('entidad_id')) {
            $query->whereHas('plan', function ($planQuery) use ($request) {
                $planQuery->where('entidad_id', $request->entidad_id);
            });
        }

        $metas = $query->get();

        // El estado es calculado por el modelo y por eso se filtra despues de consultar.
        if ($request->filled('estado')) {
            $metas = $metas->filter(function ($meta) use ($request) {
                return match ($request->estado) {
                    'completadas' => $meta->completada,
                    'pendientes' => $meta->indicadores->isEmpty(),
                    default => $meta->indicadores->isNotEmpty() && !$meta->completada,
                };
            });
        }

        $entidades = Entidad::orderBy('nombre')->get();

        return view('reportes.metas', compact('metas', 'entidades'));
    }

    public function proyectos(Request $request)
    {
        // Valida el unico filtro disponible en este reporte.
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

    public function trazabilidad()
    {
        // Carga las relaciones necesarias para imprimir la matriz completa.
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
