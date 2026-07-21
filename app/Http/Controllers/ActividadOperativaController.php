<?php

namespace App\Http\Controllers;

use App\Models\ActividadOperativa;
use App\Models\Indicador;
use App\Models\ObjetivoEstrategico;
use App\Models\Plan;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ActividadOperativaController extends Controller
{
    public function index()
    {
        $actividades = ActividadOperativa::with(['plan', 'proyecto.programa', 'objetivoEstrategico', 'indicador', 'revisor'])->orderByDesc('id')->get();

        return view('actividades-operativas.index', compact('actividades'));
    }

    public function create()
    {
        $catalogos = $this->catalogos();

        return view('actividades-operativas.create', $catalogos);
    }

    public function store(Request $request)
    {
        ActividadOperativa::create($this->guardarEvidencia($request, $this->validarActividad($request)));

        return redirect()->route('actividades-operativas.index')
            ->with('success', 'Actividad operativa creada correctamente.');
    }

    public function edit(ActividadOperativa $actividadOperativa)
    {
        $this->autorizarEdicion($actividadOperativa);
        $catalogos = $this->catalogos($actividadOperativa);

        return view('actividades-operativas.edit', array_merge(compact('actividadOperativa'), $catalogos));
    }

    public function update(Request $request, ActividadOperativa $actividadOperativa)
    {
        $this->autorizarEdicion($actividadOperativa);
        $actividadOperativa->update($this->guardarEvidencia($request, $this->validarActividad($request, $actividadOperativa->id), $actividadOperativa));

        return redirect()->route('actividades-operativas.index')
            ->with('success', 'Actividad operativa actualizada correctamente.');
    }

    public function destroy(ActividadOperativa $actividadOperativa)
    {
        $this->autorizarEdicion($actividadOperativa);
        $actividadOperativa->update(['activo' => false]);

        return redirect()->route('actividades-operativas.index')
            ->with('success', 'Actividad operativa desactivada correctamente.');
    }

    public function enviarRevision(ActividadOperativa $actividadOperativa)
    {
        $this->autorizarEdicion($actividadOperativa);

        $actividadOperativa->update([
            'estado' => 'en_revision',
            'comentario_revision' => null,
            'revisado_por' => null,
            'revisado_en' => null,
        ]);

        return redirect()->route('actividades-operativas.index')
            ->with('success', 'Actividad enviada a revisión.');
    }

    public function revisar(ActividadOperativa $actividadOperativa)
    {
        abort_unless($actividadOperativa->estado === 'en_revision', 403, 'La actividad no está disponible para revisión.');

        return view('actividades-operativas.revisar', compact('actividadOperativa'));
    }

    public function decidir(Request $request, ActividadOperativa $actividadOperativa)
    {
        abort_unless($actividadOperativa->estado === 'en_revision', 403, 'La actividad no está disponible para revisión.');

        $data = $request->validate([
            'decision' => 'required|in:aprobada,observada,rechazada',
            'comentario_revision' => 'nullable|string|max:1000',
        ]);

        if (in_array($data['decision'], ['observada', 'rechazada'], true) && blank($data['comentario_revision'])) {
            return back()->withErrors(['comentario_revision' => 'El comentario es obligatorio al observar o rechazar una actividad.'])->withInput();
        }

        $actividadOperativa->update([
            'estado' => $data['decision'],
            'comentario_revision' => $data['comentario_revision'],
            'revisado_por' => $request->user()->id,
            'revisado_en' => now(),
        ]);

        return redirect()->route('actividades-operativas.index')
            ->with('success', 'Decisión registrada correctamente.');
    }

    public function cambiarEstado(Request $request, ActividadOperativa $actividadOperativa)
    {
        $data = $request->validate([
            'estado' => 'required|in:en_ejecucion,reprogramada,finalizada,cerrada',
        ]);

        if (! in_array($data['estado'], $actividadOperativa->transicionesPermitidas(), true)) {
            return back()->withErrors(['estado' => 'El cambio de estado no está permitido.']);
        }

        if ($data['estado'] === 'finalizada' && ! $actividadOperativa->evidencia) {
            return back()->withErrors(['estado' => 'Debe adjuntar una evidencia antes de finalizar la actividad.']);
        }

        $actividadOperativa->update(['estado' => $data['estado']]);

        return redirect()->route('actividades-operativas.index')
            ->with('success', 'Estado de la actividad actualizado correctamente.');
    }

    private function validarActividad(Request $request, ?int $actividadId = null): array
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:30|unique:actividades_operativas,codigo'.($actividadId ? ','.$actividadId : ''),
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'plan_id' => 'required|exists:planes,id',
            'proyecto_id' => 'required|exists:proyectos,id',
            'objetivo_estrategico_id' => 'required|exists:objetivos_estrategicos,id',
            'indicador_id' => 'required|exists:indicadores,id',
            'responsable' => 'required|string|max:150',
            'anio' => 'required|integer|between:2020,2100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'meta_operativa' => 'required|string|max:200',
            'meta_anual' => 'required|integer|between:1,100',
            'unidad_medida' => 'required|in:%,unidad,actividad',
            'avance' => 'required|integer|between:0,100',
            'presupuesto' => 'required|numeric|min:0',
            'prioridad' => 'required|in:alta,media,baja',
            'activo' => 'required|boolean',
            'evidencia' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($data['avance'] > $data['meta_anual']) {
            throw ValidationException::withMessages([
                'avance' => 'La ejecución no puede superar la meta anual.',
            ]);
        }

        $plan = Plan::findOrFail($data['plan_id']);
        $proyecto = Proyecto::with('programa')->findOrFail($data['proyecto_id']);
        $indicador = Indicador::with('meta')->findOrFail($data['indicador_id']);

        if ($plan->entidad_id && $proyecto->programa?->entidad_id !== $plan->entidad_id) {
            throw ValidationException::withMessages([
                'proyecto_id' => 'El proyecto debe pertenecer a la misma entidad del plan seleccionado.',
            ]);
        }

        if ($indicador->meta?->plan_id !== $plan->id) {
            throw ValidationException::withMessages([
                'indicador_id' => 'El indicador debe estar asociado al plan seleccionado.',
            ]);
        }

        return $data;
    }

    private function catalogos(?ActividadOperativa $actividadOperativa = null): array
    {
        return [
            'planes' => Plan::where('activo', true)->orWhere('id', $actividadOperativa?->plan_id)->orderByDesc('id')->get(),
            'proyectos' => Proyecto::where('activo', true)->orWhere('id', $actividadOperativa?->proyecto_id)->with('programa')->orderBy('nombre')->get(),
            'objetivos' => ObjetivoEstrategico::where('activo', true)->orWhere('id', $actividadOperativa?->objetivo_estrategico_id)->orderBy('nombre')->get(),
            'indicadores' => Indicador::where('activo', true)->orWhere('id', $actividadOperativa?->indicador_id)->with('meta')->orderBy('nombre')->get(),
        ];
    }

    private function guardarEvidencia(Request $request, array $data, ?ActividadOperativa $actividadOperativa = null): array
    {
        unset($data['evidencia']);

        if ($request->hasFile('evidencia')) {
            if ($actividadOperativa?->evidencia) {
                Storage::disk('public')->delete($actividadOperativa->evidencia);
            }

            $data['evidencia'] = $request->file('evidencia')->store('poa-evidencias', 'public');
        }

        return $data;
    }

    private function autorizarEdicion(ActividadOperativa $actividadOperativa): void
    {
        abort_unless($actividadOperativa->puedeEditar(), 403, 'Solo se pueden editar actividades en estado Borrador u Observada.');
    }
}
