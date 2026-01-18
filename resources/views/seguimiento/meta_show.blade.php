<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de Meta
        </h2>
    </x-slot>

    <style>
        :root{
            --beige:#f5f1ea;
            --card:#ffffff;

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

        .top-grid{
            display:grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-top: 18px;
        }
        @media(min-width:1024px){
            .top-grid{ grid-template-columns: 1.2fr .8fr; }
        }

        .progress{
            width:100%;
            height:8px;
            background:#e5e7eb;
            border-radius:999px;
            overflow:hidden;
            margin-top:8px;
        }
        .progress div{ height:100%; }

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
            background:#fafafa;
        }
        .status-btn .pill-dot{
            width:10px; height:10px; border-radius:999px;
            border:1px solid rgba(0,0,0,.08);
        }
        .status-btn.done{ background:#eef7f5; }
        .status-btn.done .pill-dot{ background: var(--green); }
        .status-btn.progressing{ background:#fff6ee; }
        .status-btn.progressing .pill-dot{ background: var(--orange); }

        .kpis{
            display:grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin-top: 12px;
        }
        @media(max-width:640px){
            .kpis{ grid-template-columns: 1fr; }
        }
        .kpi{
            border-radius:14px;
            padding:14px;
            background:#fafafa;
            border:1px solid var(--border-soft);
        }
        .kpi .label{ font-size:12px; color:var(--muted); }
        .kpi .value{ font-size:18px; font-weight:800; color:var(--text); margin-top:2px; }

        .grid-ind{
            display:grid;
            grid-template-columns: 1fr;
            gap: 14px;
            margin-top: 16px;
        }
        @media(min-width:768px){
            .grid-ind{ grid-template-columns: 1fr 1fr; }
        }

        .indicator-card{
            transition:transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }
        .indicator-card:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 20px rgba(0,0,0,.06);
            border-color:#bfc7d2;
        }

        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            height:34px;
            padding:0 14px;
            border-radius:10px;
            border:1px solid var(--border);
            font-weight:700;
            font-size:13px;
            text-decoration:none;
            transition:background .15s ease, transform .15s ease;
        }
        .btn:hover{ transform:translateY(-1px); }

        .btn-blue{ background:#f0f4fb; color:#365a99; }
        .btn-blue:hover{ background:#e6eefc; }

        .link{
            color:#6b86c9;
            font-weight:700;
            text-decoration:none;
        }
        .link:hover{ color:#5a76be; }

        .pill{
            display:inline-flex;
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
    </style>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="wrap">

                {{-- Encabezado --}}
                <div class="flex justify-between flex-wrap gap-4 items-start">
                    <div>
                        <div class="title">{{ $meta->codigo }} – {{ $meta->nombre }}</div>
                        <div class="muted" style="margin-top:6px;">
                            Plan: <strong>{{ $meta->plan->codigo ?? 'PLAN' }}</strong> — {{ $meta->plan->nombre ?? 'Sin plan' }}
                        </div>
                        <div class="muted" style="margin-top:6px;">
                            {{ $meta->descripcion ?? 'Sin descripción.' }}
                        </div>
                    </div>

                    {{-- Leyenda --}}
                    <div class="flex gap-10 flex-wrap">
                        <div class="pill"><span class="dot blue"></span>Acción</div>
                        <div class="pill"><span class="dot green"></span>Completado</div>
                        <div class="pill"><span class="dot orange"></span>En progreso</div>
                    </div>
                </div>

                {{-- Resumen de la meta --}}
                @php
                    $p = max(0, min(100, (float)$meta->progreso));
                    $done = (bool)$meta->completada;
                @endphp

                <div class="top-grid">
                    <div class="card">
                        <div class="flex justify-between items-start gap-3">
                            <div>
                                <div class="title">Progreso de la Meta</div>
                                <div class="muted" style="margin-top:6px;">
                                    Este porcentaje se calcula automáticamente con base en el avance de sus indicadores.
                                </div>
                            </div>

                            <span class="status-btn {{ $done ? 'done' : 'progressing' }}">
                                <span class="pill-dot"></span>
                                {{ $done ? 'Completada' : 'En progreso' }}
                            </span>
                        </div>

                        <div style="margin-top:14px;">
                            <div class="flex justify-between text-sm">
                                <span class="muted">Avance</span>
                                <strong>{{ round($p) }}%</strong>
                            </div>
                            <div class="progress">
                                <div style="width:{{ $p }}%; background:{{ $done ? 'var(--green)' : 'var(--orange)' }}"></div>
                            </div>
                        </div>

                        <div class="kpis">
                            <div class="kpi" style="background:#f0f4fb">
                                <div class="label">Indicadores</div>
                                <div class="value">{{ $meta->indicadores->count() }}</div>
                            </div>
                            <div class="kpi" style="background:#eef7f5">
                                <div class="label">Completados</div>
                                <div class="value">
                                    {{ $meta->indicadores->filter(fn($i) => $i->completado)->count() }}
                                </div>
                            </div>
                            <div class="kpi" style="background:#fff6ee">
                                <div class="label">Pendientes</div>
                                <div class="value">
                                    {{ $meta->indicadores->count() - $meta->indicadores->filter(fn($i) => $i->completado)->count() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="title">Acciones</div>
                        <div class="muted" style="margin-top:6px;">
                            Registra avances en los indicadores para que el progreso se actualice automáticamente.
                        </div>

                        <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
                            <a class="btn btn-blue" href="{{ route('seguimiento.metas') }}">← Volver</a>
                        </div>
                    </div>
                </div>

                {{-- Indicadores --}}
                <div class="title" style="margin-top:22px;">Indicadores</div>
                <div class="muted" style="margin-top:6px;">
                    Cada indicador puede tener avances y evidencias. Cuando un indicador llega a 100%, se marca como completado.
                </div>

                <div class="grid-ind">
                    @forelse($meta->indicadores as $ind)
                        @php
                            $ip = max(0, min(100, (float)$ind->progreso));
                            $idone = (bool)$ind->completado;
                            $last = $ind->ultimoAvance;
                        @endphp

                        <div class="card indicator-card">
                            <div class="flex justify-between items-start gap-3">
                                <div>
                                    <div class="muted">{{ $ind->codigo }}</div>
                                    <div class="font-semibold mt-1">
                                        {{ $ind->nombre }}
                                    </div>
                                    <div class="muted" style="margin-top:6px;">
                                        {{ $ind->descripcion ?? 'Sin descripción.' }}
                                    </div>
                                </div>

                                <span class="status-btn {{ $idone ? 'done' : 'progressing' }}">
                                    <span class="pill-dot"></span>
                                    {{ $idone ? 'Completado' : 'En progreso' }}
                                </span>
                            </div>

                            <div class="kpis" style="grid-template-columns:1fr 1fr 1fr; margin-top:14px;">
                                <div class="kpi">
                                    <div class="label">Línea base</div>
                                    <div class="value">{{ $ind->linea_base ?? '-' }}</div>
                                </div>
                                <div class="kpi">
                                    <div class="label">Valor meta</div>
                                    <div class="value">{{ $ind->valor_meta ?? '-' }} {{ $ind->unidad }}</div>
                                </div>
                                <div class="kpi" style="background:#f0f4fb">
                                    <div class="label">Último valor</div>
                                    <div class="value">{{ $last?->valor_reportado ?? '-' }} {{ $ind->unidad }}</div>
                                </div>
                            </div>

                            <div style="margin-top:14px;">
                                <div class="flex justify-between text-sm">
                                    <span class="muted">Avance</span>
                                    <strong>{{ round($ip) }}%</strong>
                                </div>
                                <div class="progress">
                                    <div style="width:{{ $ip }}%;
                                        background:{{ $idone ? 'var(--green)' : 'var(--orange)' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center mt-4 text-sm">
                                <span class="muted">
                                    @if($last && $last->evidencia_path)
                                        <a class="link" href="{{ asset('storage/'.$last->evidencia_path) }}" target="_blank">
                                            Ver evidencia →
                                        </a>
                                    @else
                                        Sin evidencia registrada
                                    @endif
                                </span>

                                <a class="btn btn-blue" href="{{ route('indicadores.avance.create', $ind->id) }}">
                                    Registrar avance
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="card">
                            <div class="title">Sin indicadores</div>
                            <div class="muted" style="margin-top:6px;">
                                Esta meta no tiene indicadores. Agrégalos en Administración.
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

