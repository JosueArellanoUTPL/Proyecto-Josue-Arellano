<x-app-layout>
    {{-- Seccion de encabezado. --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar Avance de Indicador
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="wrap">

                <div class="card">
                    <div class="title">{{ $indicador->codigo }} — {{ $indicador->nombre }}</div>
                    <div class="muted" style="margin-top:6px;">
                        Meta: <strong>{{ $indicador->meta->codigo }}</strong> — {{ $indicador->meta->nombre }}
                    </div>
                    <div class="muted" style="margin-top:6px;">
                        Plan: <strong>{{ $indicador->meta->plan->codigo ?? 'PLAN' }}</strong> — {{ $indicador->meta->plan->nombre ?? 'Sin plan' }}
                    </div>

                    <div class="kpis">
                        <div class="kpi">
                            <div class="label">Línea base</div>
                            <div class="value">{{ $indicador->linea_base ?? '-' }} {{ $indicador->unidad }}</div>
                        </div>
                        <div class="kpi" style="background:#f0f4fb">
                            <div class="label">Valor meta</div>
                            <div class="value">{{ $indicador->valor_meta ?? '-' }} {{ $indicador->unidad }}</div>
                        </div>
                        <div class="kpi" style="background:#fff6ee">
                            <div class="label">Avance actual</div>
                            <div class="value">{{ round((float)$indicador->progreso,0) }}%</div>
                        </div>
                    </div>
                </div>

                <div style="height:14px"></div>

                @if ($errors->any())
                    <div class="error">
                        <strong>Revisa los campos:</strong>
                        <ul style="margin-left:18px; margin-top:6px; list-style:disc;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('indicadores.avance.store', $indicador->id) }}"
                      enctype="multipart/form-data"
                      class="card">
                    @csrf

                    <div class="title">Nuevo avance</div>
                    <div class="muted" style="margin-top:6px;">
                        Registra el valor alcanzado y sube una evidencia (PDF/JPG/PNG).
                    </div>

                    <div style="display:grid; grid-template-columns:1fr; gap:12px; margin-top:14px;">
                        <div>
                            <label class="muted">Fecha</label>
                            <input class="input" type="date" name="fecha"
                                   value="{{ old('fecha', date('Y-m-d')) }}">
                        </div>

                        <div>
                            <label class="muted">Valor reportado ({{ $indicador->unidad }})</label>
                            <input class="input" type="number" step="0.01" name="valor_reportado"
                                   value="{{ old('valor_reportado') }}" required>
                        </div>

                        <div>
                            <label class="muted">Comentario (opcional)</label>
                            <textarea class="textarea" name="comentario">{{ old('comentario') }}</textarea>
                        </div>

                        <div>
                            <label class="muted">Evidencia (PDF/JPG/PNG, máx. 5MB)</label>
                            <input class="file" type="file" name="evidencia">
                            <div class="muted" style="margin-top:6px;">
                                Ejemplo: captura, acta, reporte o documento de respaldo.
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-4">
                        <a class="btn btn-blue" href="{{ route('seguimiento.meta.show', $indicador->meta_id) }}">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-green">
                            Guardar avance
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
