<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\ActividadOperativa;

class SeguimientoController extends Controller
{
    // Listar metas para seguimiento.
    public function index()
    {
        $metas = Meta::where('activo', 1)->with([
            'plan.entidad',
            'indicadores' => fn ($query) => $query->where('activo', 1)->with('ultimoAvance'),
        ])->orderBy('id', 'desc')->get();

        // Calculo del resumen de metas.
        $metasMedibles = $metas->filter(fn ($meta) => $meta->indicadores->isNotEmpty());
        $resumen = [
            'total' => $metas->count(),
            'pendientes' => $metas->count() - $metasMedibles->count(),
            'completadas' => $metasMedibles->filter(fn ($meta) => $meta->completada)->count(),
            'progreso_global' => $metasMedibles->count()
                ? (int) round($metasMedibles->avg(fn ($meta) => (float) $meta->progreso))
                : 0,
        ];
        $resumen['en_progreso'] = $metasMedibles->count() - $resumen['completadas'];

        return view('seguimiento.metas', compact(
            'metas',
            'resumen'
        ));
    }

    // Mostrar seguimiento de una meta.
    public function show(Meta $meta)
    {
        $meta->load([
            'plan',
            'indicadores.ultimoAvance',
            'indicadores.avances',
        ]);

        return view('seguimiento.meta_show', compact('meta'));
    }

    public function poa()
    {
        $actividades = ActividadOperativa::where('activo', true)->with([
            'plan.pdn',
            'proyecto.programa',
            'objetivoEstrategico',
            'indicador',
        ])->orderByDesc('updated_at')->get();

        $resumen = [
            'total' => $actividades->count(),
            'en_ejecucion' => $actividades->whereIn('estado', ['aprobada', 'en_ejecucion', 'reprogramada'])->count(),
            'finalizadas' => $actividades->whereIn('estado', ['finalizada', 'cerrada'])->count(),
            'con_evidencia' => $actividades->filter(fn ($actividad) => filled($actividad->evidencia))->count(),
            'avance_promedio' => $actividades->count()
                ? (int) round($actividades->avg(fn ($actividad) => ($actividad->avance / max(1, $actividad->meta_anual)) * 100))
                : 0,
        ];

        return view('seguimiento.poa', compact('actividades', 'resumen'));
    }
}
