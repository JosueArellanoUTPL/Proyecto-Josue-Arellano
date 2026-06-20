<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use App\Models\Meta;
use Illuminate\Http\Request;

class SeguimientoController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'entidad_id' => ['nullable', 'exists:entidades,id'],
        ]);

        // Vista de seguimiento: lista metas con plan, indicadores y ultimo avance.
        $query = Meta::where('activo', 1)->with([
                'plan.entidad',
                'indicadores' => fn ($query) => $query->where('activo', 1)->with('ultimoAvance'),
                'proyectos' => fn ($query) => $query->where('activo', 1)->with('ultimoAvance'),
            ]);

        // La entidad se obtiene desde el plan al que pertenece cada meta.
        if ($request->filled('entidad_id')) {
            $query->whereHas('plan', function ($planQuery) use ($request) {
                $planQuery->where('entidad_id', $request->entidad_id);
            });
        }

        $metas = $query->orderBy('id', 'desc')->get();
        $entidades = Entidad::where('activo', 1)->orderBy('nombre')->get();
        $entidadSeleccionada = $request->query('entidad_id');

        // Solo las metas con indicadores activos pueden tener avance.
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
            'resumen',
            'entidades',
            'entidadSeleccionada'
        ));
    }

    public function show(Meta $meta)
    {
        // Detalle de una meta: carga indicadores, ultimo avance e historial.
        $meta->load([
            'plan',
            'indicadores.ultimoAvance',
            'indicadores.avances',
            'proyectos' => fn ($query) => $query->where('activo', 1)->with('ultimoAvance')
        ]);

        return view('seguimiento.meta_show', compact('meta'));
    }
}
