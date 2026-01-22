<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Seguimiento de Programa
        </h2>
    </x-slot>

    <style>
        :root{
            --beige:#f5f1ea; --card:#ffffff; --border:#cfd5dd; --border-soft:#d9dee6;
            --blue:#8faadc; --green:#9fd3c7; --orange:#f4c095;
            --text:#1f2937; --muted:#6b7280;
        }
        .wrap{ background:var(--beige); border-radius:20px; padding:28px; border:1px solid var(--border-soft); }
        .card{ background:var(--card); border-radius:18px; padding:18px; border:1px solid var(--border); }
        .title{ font-weight:700; font-size:18px; color:var(--text); }
        .muted{ color:var(--muted); font-size:13px; }
        .row{ display:flex; justify-content:space-between; gap:10px; align-items:flex-start; }
        .btn{
            display:inline-flex; align-items:center; justify-content:center;
            height:34px; padding:0 14px; border-radius:10px;
            border:1px solid var(--border);
            font-weight:700; font-size:13px; text-decoration:none;
            background:#f0f4fb; color:#365a99;
        }
        .btn:hover{ background:#e6eefc; }
        .badge{ font-size:12px; font-weight:700; padding:6px 10px; border-radius:12px; border:1px solid var(--border); }
        .badge.green{ background:#eef7f5; }
        .badge.orange{ background:#fff6ee; }

        .progress{ width:100%; height:8px; background:#e5e7eb; border-radius:999px; overflow:hidden; margin-top:8px; }
        .progress div{ height:100%; }

        .kpis{ display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:12px; }
        @media(max-width:768px){ .kpis{ grid-template-columns:1fr; } }
        .kpi{ border-radius:14px; padding:12px; background:#fafafa; border:1px solid var(--border-soft); }
        .kpi .label{ font-size:12px; color:var(--muted); }
        .kpi .value{ font-size:18px; font-weight:800; color:var(--text); }

        .list{ margin-top:12px; display:grid; gap:10px; }
        .click-card{
            padding:10px; border-radius:14px; border:1px solid var(--border-soft);
            background:#fafafa; transition:all .15s ease-in-out;
        }
        .click-card:hover{ transform:translateY(-1px); background:#f6f8fb; border-color:var(--border); }
    </style>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                <div class="row">
                    <div>
                        <div class="title">{{ $programa->nombre }}</div>
                        <div class="muted" style="margin-top:6px;">
                            Entidad: <strong>{{ $programa->entidad->nombre ?? '—' }}</strong>
                        </div>
                        <div class="muted" style="margin-top:6px;">
                            {{ $programa->descripcion ?? 'Sin descripción.' }}
                        </div>
                    </div>

                    <a class="btn" href="{{ route('seguimiento.organizacion.entidad', $programa->entidad_id) }}">
                        ← Volver a Entidad
                    </a>
                </div>

                @php $p = max(0, min(100, (int)$progresoPrograma)); @endphp

                <div class="card" style="margin-top:16px;">
                    <div class="row" style="align-items:center;">
                        <div>
                            <div class="title">Avance del Programa</div>
                            <div class="muted" style="margin-top:6px;">
                                Próximo paso: conectar este avance a “Avances de Proyecto” con evidencias.
                            </div>
                        </div>
                        <span class="badge {{ $p >= 100 ? 'green' : 'orange' }}">{{ $p }}%</span>
                    </div>

                    <div class="progress">
                        <div style="width:{{ $p }}%; background:{{ $p >= 100 ? 'var(--green)' : 'var(--orange)' }}"></div>
                    </div>

                    <div class="kpis">
                        <div class="kpi" style="background:#f0f4fb">
                            <div class="label">Total proyectos</div>
                            <div class="value">{{ $kpiProyectos }}</div>
                        </div>
                        <div class="kpi" style="background:#eef7f5">
                            <div class="label">Proyectos activos</div>
                            <div class="value">{{ $kpiActivos }}</div>
                        </div>
                        <div class="kpi">
                            <div class="label">Proyectos inactivos</div>
                            <div class="value">{{ $kpiInactivos }}</div>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-top:14px;">
                    <div class="title">Proyectos del Programa</div>
                    <div class="muted" style="margin-top:6px;">Haz clic en un proyecto para ver su seguimiento.</div>

                    <div class="list">
                        @forelse($programa->proyectos as $pry)
                            <a href="{{ route('seguimiento.proyecto.show', $pry->id) }}" style="text-decoration:none;">
                                <div class="click-card" style="display:flex; justify-content:space-between; gap:10px; align-items:center;">
                                    <div>
                                        <strong style="color:var(--text)">{{ $pry->nombre }}</strong>
                                        <div class="muted" style="margin-top:4px;">
                                            {{ $pry->descripcion ? \Illuminate\Support\Str::limit($pry->descripcion, 80) : 'Sin descripción.' }}
                                        </div>
                                    </div>
                                    <span class="badge {{ $pry->activo ? 'green' : 'orange' }}">
                                        {{ $pry->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="muted">No hay proyectos en este programa.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
