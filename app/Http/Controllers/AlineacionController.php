<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Entidad;
use App\Models\Meta;
use App\Models\ObjetivoEstrategico;
use App\Models\Ods;
use Illuminate\Http\Request;

class AlineacionController extends Controller
{
    // Listar alineaciones.
    public function index(Request $request)
    {
        $request->validate([
            'entidad_id' => ['nullable', 'exists:entidades,id'],
            'meta_id' => ['nullable', 'exists:metas,id'],
        ]);

        $entidadSeleccionada = $request->query('entidad_id');
        $metaSeleccionada = $request->query('meta_id');
        $entidades = Entidad::where('activo', true)->orderBy('nombre')->get();

        // Filtro de metas por entidad.
        $metas = Meta::with('plan')
            ->where('activo', true)
            ->when($entidadSeleccionada, function ($query) use ($entidadSeleccionada) {
                $query->whereHas('plan', fn ($plan) => $plan->where('entidad_id', $entidadSeleccionada));
            }, fn ($query) => $query->whereRaw('1 = 0'))
            ->orderBy('codigo')
            ->get();

        // Validacion de entidad y meta.
        if ($metaSeleccionada && ! $metas->contains('id', (int) $metaSeleccionada)) {
            $metaSeleccionada = null;
        }

        $alineaciones = Alineacion::with(['meta.plan.entidad', 'meta.plan.pdn', 'ods', 'objetivoEstrategico'])
            ->when($entidadSeleccionada, function ($query) use ($entidadSeleccionada) {
                $query->whereHas('meta.plan', fn ($plan) => $plan->where('entidad_id', $entidadSeleccionada));
            })
            ->when($metaSeleccionada, fn ($query) => $query->where('meta_id', $metaSeleccionada))
            ->orderBy('id', 'desc')
            ->get();

        return view('alineaciones.index', compact(
            'alineaciones',
            'entidades',
            'metas',
            'entidadSeleccionada',
            'metaSeleccionada'
        ));
    }

    // Mostrar formulario para crear alineacion.
    public function create()
    {
        $entidades = Entidad::where('activo', true)->orderBy('nombre')->get();
        $metas = Meta::with(['plan.pdn', 'plan.entidad'])->where('activo', true)->orderBy('id', 'desc')->get();
        $ods = Ods::where('activo', true)->orderBy('id', 'desc')->get();
        $objetivos = ObjetivoEstrategico::where('activo', true)->orderBy('id', 'desc')->get();

        return view('alineaciones.create', compact('entidades', 'metas', 'ods', 'objetivos'));
    }

    // Guardar alineacion.
    public function store(Request $request)
    {
        // Validacion de alineacion.
        $data = $request->validate([
            'meta_id' => ['required', 'exists:metas,id'],
            'ods_id' => ['nullable', 'exists:ods,id'],
            'objetivo_estrategico_id' => ['nullable', 'exists:objetivos_estrategicos,id'],

            'activo' => ['nullable'],
        ]);

        // Estado activo.
        $data['activo'] = $request->has('activo');

        if (empty($data['ods_id']) && empty($data['objetivo_estrategico_id'])) {
            return back()
                ->withErrors(['ods_id' => 'Selecciona al menos un ODS o un objetivo estratégico. El PND se toma del plan.'])
                ->withInput();
        }

        Alineacion::create($data);

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineacion creada correctamente.');
    }

    // Mostrar formulario para editar alineacion.
    public function edit(Alineacion $alineacion)
    {
        $alineacion->load('meta.plan');
        $entidades = Entidad::where('activo', true)
            ->orWhere('id', $alineacion->meta?->plan?->entidad_id)
            ->orderBy('nombre')
            ->get();
        $metas = Meta::with(['plan.pdn', 'plan.entidad'])->where('activo', true)->orWhere('id', $alineacion->meta_id)->orderBy('id', 'desc')->get();
        $ods = Ods::where('activo', true)->orWhere('id', $alineacion->ods_id)->orderBy('id', 'desc')->get();
        $objetivos = ObjetivoEstrategico::where('activo', true)->orWhere('id', $alineacion->objetivo_estrategico_id)->orderBy('id', 'desc')->get();

        return view('alineaciones.edit', compact('alineacion', 'entidades', 'metas', 'ods', 'objetivos'));
    }

    // Actualizar alineacion.
    public function update(Request $request, Alineacion $alineacion)
    {
        // Validacion de alineacion.
        $data = $request->validate([
            'meta_id' => ['required', 'exists:metas,id'],
            'ods_id' => ['nullable', 'exists:ods,id'],
            'objetivo_estrategico_id' => ['nullable', 'exists:objetivos_estrategicos,id'],

            'activo' => ['nullable'],
        ]);

        $data['activo'] = $request->has('activo');

        if (empty($data['ods_id']) && empty($data['objetivo_estrategico_id'])) {
            return back()
                ->withErrors(['ods_id' => 'Selecciona al menos un ODS o un objetivo estratégico. El PND se toma del plan.'])
                ->withInput();
        }

        $alineacion->update($data);

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineacion actualizada correctamente.');
    }

    // Eliminar alineacion.
    public function destroy(Alineacion $alineacion)
    {
        // Desactivacion para conservar trazabilidad.
        $alineacion->update(['activo' => false]);

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineación desactivada correctamente.');
    }
}
