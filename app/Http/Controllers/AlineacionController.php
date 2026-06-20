<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Entidad;
use App\Models\Meta;
use App\Models\Ods;
use App\Models\ObjetivoEstrategico;
use Illuminate\Http\Request;

class AlineacionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'entidad_id' => ['nullable', 'exists:entidades,id'],
            'meta_id' => ['nullable', 'exists:metas,id'],
        ]);

        $entidadSeleccionada = $request->query('entidad_id');
        $metaSeleccionada = $request->query('meta_id');
        $entidades = Entidad::where('activo', true)->orderBy('nombre')->get();

        // Las metas aparecen despues de seleccionar una entidad.
        $metas = Meta::with('plan')
            ->where('activo', true)
            ->when($entidadSeleccionada, function ($query) use ($entidadSeleccionada) {
                $query->whereHas('plan', fn ($plan) => $plan->where('entidad_id', $entidadSeleccionada));
            }, fn ($query) => $query->whereRaw('1 = 0'))
            ->orderBy('codigo')
            ->get();

        // Evita combinar por URL una meta que no pertenece a la entidad filtrada.
        if ($metaSeleccionada && !$metas->contains('id', (int) $metaSeleccionada)) {
            $metaSeleccionada = null;
        }

        // El listado se filtra por la entidad del plan y luego por meta.
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

    public function create()
    {
        // Catalogos activos para armar una alineacion estrategica.
        $entidades = Entidad::where('activo', true)->orderBy('nombre')->get();
        $metas = Meta::with(['plan.pdn', 'plan.entidad'])->where('activo', true)->orderBy('id', 'desc')->get();
        $ods = Ods::where('activo', true)->orderBy('id', 'desc')->get();
        $objetivos = ObjetivoEstrategico::where('activo', true)->orderBy('id', 'desc')->get();

        return view('alineaciones.create', compact('entidades', 'metas', 'ods', 'objetivos'));
    }

    public function store(Request $request)
    {
        // Valida la meta y los instrumentos estrategicos.
        $data = $request->validate([
            'meta_id' => ['required', 'exists:metas,id'],
            'ods_id' => ['nullable', 'exists:ods,id'],
            'objetivo_estrategico_id' => ['nullable', 'exists:objetivo_estrategicos,id'],

            'activo' => ['nullable'],
        ]);

        // Checkbox activo convertido a true/false.
        $data['activo'] = $request->has('activo');

        if (empty($data['ods_id']) && empty($data['objetivo_estrategico_id'])) {
            return back()
                ->withErrors(['ods_id' => 'Selecciona al menos un ODS o un objetivo estratégico. El PND se toma del plan.'])
                ->withInput();
        }

        // Guarda la alineacion estrategica.
        Alineacion::create($data);

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineacion creada correctamente.');
    }

    public function edit(Alineacion $alineacion)
    {
        // Cargo los mismos catalogos para editar una alineacion.
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

    public function update(Request $request, Alineacion $alineacion)
    {
        // Mismas reglas de crear, pero para actualizar.
        $data = $request->validate([
            'meta_id' => ['required', 'exists:metas,id'],
            'ods_id' => ['nullable', 'exists:ods,id'],
            'objetivo_estrategico_id' => ['nullable', 'exists:objetivo_estrategicos,id'],

            'activo' => ['nullable'],
        ]);

        $data['activo'] = $request->has('activo');

        if (empty($data['ods_id']) && empty($data['objetivo_estrategico_id'])) {
            return back()
                ->withErrors(['ods_id' => 'Selecciona al menos un ODS o un objetivo estratégico. El PND se toma del plan.'])
                ->withInput();
        }

        // Actualiza la alineacion seleccionada.
        $alineacion->update($data);

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineacion actualizada correctamente.');
    }

    public function destroy(Alineacion $alineacion)
    {
        // Se desactiva para conservar la trazabilidad estratégica.
        $alineacion->update(['activo' => false]);

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineación desactivada correctamente.');
    }
}
