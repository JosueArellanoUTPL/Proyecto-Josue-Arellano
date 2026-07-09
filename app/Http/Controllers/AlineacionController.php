<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Entidad;
use App\Models\Meta;
use App\Models\ObjetivoEstrategico;
use App\Models\Ods;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AlineacionController extends Controller
{
    // Listar alineaciones.
    public function index()
    {
        // Listado simple sin filtros.
        $alineaciones = Alineacion::with(['meta.plan.entidad', 'meta.plan.pdn', 'ods', 'objetivoEstrategico'])
            ->orderBy('id', 'desc')
            ->get();

        return view('alineaciones.index', compact('alineaciones'));
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
        $data = $this->validarAlineacion($request);

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
        $data = $this->validarAlineacion($request);

        $alineacion->update($data);

        return redirect()
            ->route('alineaciones.index')
            ->with('success', 'Alineacion actualizada correctamente.');
    }

    // Validacion de alineacion.
    private function validarAlineacion(Request $request): array
    {
        $data = $request->validate([
            'meta_id' => ['required', 'exists:metas,id'],
            'ods_id' => ['nullable', 'exists:ods,id'],
            'objetivo_estrategico_id' => ['nullable', 'exists:objetivos_estrategicos,id'],
            'activo' => ['nullable'],
        ]);

        if (empty($data['ods_id']) && empty($data['objetivo_estrategico_id'])) {
            throw ValidationException::withMessages([
                'ods_id' => 'Selecciona al menos un ODS o un objetivo estratégico. El PND se toma del plan.',
            ]);
        }

        $data['activo'] = $request->has('activo');

        return $data;
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
