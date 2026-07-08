<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use App\Models\Meta;
use Illuminate\Http\Request;

class SeguimientoController extends Controller
{
    // Listar metas para seguimiento.
    public function index(Request $request)
    {
        $request->validate([
            'entidad_id' => ['nullable', 'exists:entidades,id'],
        ]);

        $query = Meta::where('activo', 1)->with([
            'plan.entidad',
            'indicadores' => fn ($query) => $query->where('activo', 1)->with('ultimoAvance'),
        ]);

        if ($request->filled('entidad_id')) {
            $query->whereHas('plan', function ($planQuery) use ($request) {
                $planQuery->where('entidad_id', $request->entidad_id);
            });
        }

        $metas = $query->orderBy('id', 'desc')->get();
        $entidades = Entidad::where('activo', 1)->orderBy('nombre')->get();
        $entidadSeleccionada = $request->query('entidad_id');

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
            'resumen',
            'entidades',
            'entidadSeleccionada'
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
}
