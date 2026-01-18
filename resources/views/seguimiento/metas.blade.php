<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Seguimiento de Metas
        </h2>
    </x-slot>

    <style>
        :root{
            --beige:#f5f1ea;
            --card:#ffffff;

            /* Bordes más visibles */
            --border:#cfd5dd;
            --border-soft:#d9dee6;

            --blue:#8faadc;
            --green:#9fd3c7;
            --orange:#f4c095;

            --text:#1f2937;
            --muted:#6b7280;
        }

        .wrap{
            background: var(--beige);
            border-radius: 20px;
            padding: 28px;
            border: 1px solid var(--border-soft);
        }

        /* Leyenda mejorada (en vez de chips blancos) */
        .legend{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
            justify-content:flex-end;
        }
        .legend-item{
            display:flex;
            align-items:center;
            gap:8px;
            font-size:12px;
            padding:8px 12px;
            border-radius:12px;
            background:#fff;
            border:1px solid var(--border);
            color: var(--text);
        }
        .dot{
            width:10px; height:10px; border-radius:999px;
            border:1px solid rgba(0,0,0,.08);
        }
        .dot.blue{ background: var(--blue); }
        .dot.green{ background: var(--green); }
        .dot.orange{ background: var(--orange); }

        .grid-main{
            display:grid;
            grid-template-columns: 1fr;
            gap:20px;
            margin-top:24px;
        }
        @media(min-width:1024px){
            .grid-main{ grid-template-columns: 360px 1fr; }
        }

        .card{
            background:var(--card);
            border-radius:18px;
            padding:18px;
            border:1px solid var(--border);
        }

        .title{
            font-weight:700;
            font-size:18px;
            color:var(--text);
        }
        .muted{
            color:var(--muted);
            font-size:13px;
        }

        .kpis{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:12px;
            margin-top:16px;
        }
        .kpi{
            border-radius:14px;
            padding:14px;
            background:#fafafa;
            border:1px solid var(--border-soft);
        }
        .kpi .label{ font-size:12px; color:var(--muted); }
        .kpi .value{ font-size:22px; font-weight:800; color:var(--text); }

        .progress{
            width:100%;
            height:8px;
            background:#e5e7eb;
            border-radius:999px;
            overflow:hidden;
            margin-top:8px;
        }
        .progress div{ height:100%; }

        .metas{
            display:grid;
            grid-template-columns:1fr;
            gap:14px;
        }
        @media(min-width:768px){
            .metas{ grid-template-columns:1fr 1fr; }
        }

        .meta-card{
            text-decoration:none;
            color:inherit;
            transition:transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }
        .meta-card:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 20px rgba(0,0,0,.06);
            border-color:#bfc7d2;
        }

        /* Badge tipo botón, tamaño fijo, no círculos raros */
        .status-btn{
            display:inline-flex;
            align-items:center;
            gap:8px;
            height:32px;
            padding:0 12px;
            border-radius:10px;
            border:1px solid var(--border);
            font-size:12px;
            font-weight:600;
            white-space:nowrap;
        }
        .status-btn .pill-dot{
            width:10px; height:10px; border-radius:999px;
            border:1px solid rgba(0,0,0,.08);
        }

        .status-btn.done{ background:#eef7f5; }
        .status-btn.done .pill-dot{ background: var(--green); }

        .status-btn.progressing{ background:#fff6ee; }
        .status-btn.progressing .pill-dot{ background: var(--orange); }

        .link{
            color: #6b86c9;
            font-weight:700;
        }
        .link:hover{ color:#5a76be; }
    </style>

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
                    <div class="metas">
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


