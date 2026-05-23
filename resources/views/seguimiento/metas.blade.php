<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Seguimiento de Metas
        </h2>
    </x-slot>
    {{-- Los estilos reutilizables de esta vista ahora estan en resources/css/app.css --}}

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="wrap">

                <div class="flex justify-between flex-wrap gap-4">
                    <div>
                        <div class="title">Panel de Seguimiento</div>
                        <div class="muted">
                            Visualiza el avance de las metas según indicadores y evidencias.
                        </div>
                    </div>

                    {{-- Leyenda clara y con color --}}
                    <div class="legend">
                        <div class="legend-item">
                            <span class="dot blue"></span>
                            <span>Acción / Navegación</span>
                        </div>
                        <div class="legend-item">
                            <span class="dot green"></span>
                            <span>Completado</span>
                        </div>
                        <div class="legend-item">
                            <span class="dot orange"></span>
                            <span>En progreso</span>
                        </div>
                    </div>
                </div>

                <div class="grid-main">

                    {{-- Resumen --}}
                    <div class="card">
                        <div class="title">Resumen general</div>

                        @php
                            $total = $metas->count();
                            $completas = $metas->filter(fn($m) => $m->completada)->count();
                            $enProgreso = $total - $completas;
                            $pct = $total > 0 ? round(($completas / $total) * 100, 0) : 0;
                        @endphp

                        <div class="kpis">
                            <div class="kpi">
                                <div class="label">Metas</div>
                                <div class="value">{{ $total }}</div>
                            </div>
                            <div class="kpi" style="background:#eef7f5">
                                <div class="label">Completadas</div>
                                <div class="value">{{ $completas }}</div>
                            </div>
                            <div class="kpi" style="background:#fff6ee">
                                <div class="label">En progreso</div>
                                <div class="value">{{ $enProgreso }}</div>
                            </div>
                            <div class="kpi" style="background:#f0f4fb">
                                <div class="label">% Cumplimiento</div>
                                <div class="value">{{ $pct }}%</div>
                            </div>
                        </div>

                        <div style="margin-top:18px">
                            <div class="muted">Progreso global</div>
                            <div class="progress">
                                <div style="width:{{ $pct }}%; background:var(--green)"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Metas --}}
                    <div class="metas-grid">
                        @forelse($metas as $meta)
                            @php
                                $progreso = max(0, min(100, (float)$meta->progreso));
                                $done = (bool)$meta->completada;
                            @endphp

                            <a href="{{ route('seguimiento.meta.show', $meta->id) }}"
                               class="card meta-card">

                                <div class="flex justify-between items-start gap-3">
                                    <div>
                                        <div class="muted">
                                            {{ $meta->plan->codigo ?? 'PLAN' }}
                                        </div>
                                        <div class="font-semibold mt-1">
                                            {{ $meta->codigo }} – {{ $meta->nombre }}
                                        </div>
                                    </div>

                                    {{-- Badge tipo botón fijo --}}
                                    <span class="status-btn {{ $done ? 'done' : 'progressing' }}">
                                        <span class="pill-dot"></span>
                                        {{ $done ? 'Completada' : 'En progreso' }}
                                    </span>
                                </div>

                                <div class="mt-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="muted">Avance</span>
                                        <strong>{{ round($progreso) }}%</strong>
                                    </div>
                                    <div class="progress">
                                        <div style="width:{{ $progreso }}%;
                                            background:{{ $done ? 'var(--green)' : 'var(--orange)' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center mt-4 text-sm">
                                    <span class="muted">
                                        Indicadores: <strong>{{ $meta->indicadores->count() }}</strong>
                                    </span>
                                    <span class="link">Ver detalle →</span>
                                </div>
                            </a>
                        @empty
                            <div class="card">
                                <div class="title">Sin metas</div>
                                <div class="muted" style="margin-top:6px;">
                                    No hay metas registradas para seguimiento.
                                </div>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
