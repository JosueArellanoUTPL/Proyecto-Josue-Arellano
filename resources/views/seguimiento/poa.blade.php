<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Seguimiento del POA</h2></x-slot>

    <div class="py-10"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8"><div class="wrap">
        <div class="flex justify-between flex-wrap gap-4">
            <div><div class="title">Actividades operativas</div><div class="muted">Avance, estado y relación con la planificación institucional.</div></div>
            @if(auth()->user()->canManagePlanning())<a class="btn" href="{{ route('actividades-operativas.index') }}">Gestionar POA</a>@endif
        </div>

        <div class="kpis seguimiento-kpis">
            <div class="kpi"><div class="label">Actividades</div><div class="value">{{ $resumen['total'] }}</div></div>
            <div class="kpi" style="background:#f0f4fb"><div class="label">En ejecución</div><div class="value">{{ $resumen['en_ejecucion'] }}</div></div>
            <div class="kpi" style="background:#eef7f5"><div class="label">Finalizadas</div><div class="value">{{ $resumen['finalizadas'] }}</div></div>
            <div class="kpi" style="background:#fff6ee"><div class="label">Con evidencia</div><div class="value">{{ $resumen['con_evidencia'] }}</div></div>
            <div class="kpi"><div class="label">Avance promedio</div><div class="value">{{ $resumen['avance_promedio'] }}%</div></div>
        </div>

        <div class="metas-grid seguimiento-metas-grid">
            @forelse($actividades as $actividad)
                @php
                    $progreso = min(100, round(($actividad->avance / max(1, $actividad->meta_anual)) * 100));
                    $finalizada = in_array($actividad->estado, ['finalizada', 'cerrada']);
                    $enProceso = in_array($actividad->estado, ['aprobada', 'en_ejecucion', 'reprogramada']);
                    $claseEstado = $finalizada ? 'done' : ($enProceso ? 'progressing' : 'pending');
                @endphp
                <div class="card meta-card">
                    <div class="flex justify-between items-start gap-3">
                        <div><div class="muted">{{ $actividad->codigo }} · {{ $actividad->anio }}</div><div class="font-semibold mt-1">{{ $actividad->nombre }}</div></div>
                        <span class="status-btn {{ $claseEstado }}"><span class="pill-dot"></span>{{ \App\Models\ActividadOperativa::ESTADOS[$actividad->estado] }}</span>
                    </div>

                    <div class="mt-4"><div class="flex justify-between text-sm"><span class="muted">Avance físico</span><strong>{{ $actividad->avance }} / {{ $actividad->meta_anual }} {{ $actividad->unidad_medida }}</strong></div><div class="progress"><div style="width:{{ $progreso }}%; background:{{ $finalizada ? 'var(--green)' : 'var(--blue)' }}"></div></div></div>

                    <div class="trace-instruments">
                        <div class="trace-instrument"><span>Plan · PND</span><div>{{ $actividad->plan->codigo }} · {{ $actividad->plan->pdn?->codigo ?? 'Sin PND' }}</div></div>
                        <div class="trace-instrument"><span>Proyecto · Programa</span><div>{{ $actividad->proyecto->codigo }} · {{ $actividad->proyecto->programa?->codigo }}</div></div>
                        <div class="trace-instrument"><span>Objetivo estratégico</span><div>{{ $actividad->objetivoEstrategico->codigo }}</div></div>
                        <div class="trace-instrument"><span>Indicador</span><div>{{ $actividad->indicador->codigo }}</div></div>
                        <div class="trace-instrument"><span>Periodo</span><div>{{ $actividad->fecha_inicio->format('d/m/Y') }} – {{ $actividad->fecha_fin->format('d/m/Y') }}</div></div>
                        <div class="trace-instrument"><span>Evidencia</span><div>{{ $actividad->evidencia ? 'Adjunta' : 'Pendiente' }}</div></div>
                    </div>

                    <div class="flex justify-between items-center mt-4 text-sm"><span class="muted">Responsable: <strong>{{ $actividad->responsable }}</strong></span><span class="muted">Prioridad: <strong>{{ ucfirst($actividad->prioridad) }}</strong></span></div>
                </div>
            @empty
                <div class="card"><div class="title">Sin actividades POA</div><div class="muted" style="margin-top:6px;">Registra una actividad operativa para visualizar su seguimiento.</div></div>
            @endforelse
        </div>
    </div></div></div>
</x-app-layout>
