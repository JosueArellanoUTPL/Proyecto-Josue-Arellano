<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Organización (Entidades)
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

        .wrap{ background:var(--beige); border-radius:20px; padding:28px; border:1px solid var(--border-soft); }
        .card{ background:var(--card); border-radius:18px; padding:18px; border:1px solid var(--border); }
        .title{ font-weight:700; font-size:18px; color:var(--text); }
        .muted{ color:var(--muted); font-size:13px; }

        .grid{ display:grid; grid-template-columns:1fr; gap:14px; margin-top:16px; }
        @media(min-width:768px){ .grid{ grid-template-columns:1fr 1fr; } }

        .progress{ width:100%; height:8px; background:#e5e7eb; border-radius:999px; overflow:hidden; margin-top:8px; }
        .progress div{ height:100%; }

        .kpis{ display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-top:12px; }
        @media(max-width:640px){ .kpis{ grid-template-columns:1fr 1fr; } }
        .kpi{ border-radius:14px; padding:12px; background:#fafafa; border:1px solid var(--border-soft); }
        .kpi .label{ font-size:12px; color:var(--muted); }
        .kpi .value{ font-size:18px; font-weight:800; color:var(--text); }

        .link{ color:#6b86c9; font-weight:700; text-decoration:none; }
        .link:hover{ color:#5a76be; }

        .status-btn{
            display:inline-flex; align-items:center; gap:8px;
            height:32px; padding:0 12px; border-radius:10px;
            border:1px solid var(--border); font-size:12px; font-weight:600; white-space:nowrap;
        }
        .pill-dot{ width:10px; height:10px; border-radius:999px; border:1px solid rgba(0,0,0,.08); }
        .status-btn.done{ background:#eef7f5; }
        .status-btn.done .pill-dot{ background: var(--green); }
        .status-btn.progressing{ background:#fff6ee; }
        .status-btn.progressing .pill-dot{ background: var(--orange); }
    </style>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">
                <div class="title">Mapa Organizacional</div>
                <div class="muted" style="margin-top:6px;">
                    Visualiza Entidades con sus Programas/Proyectos y el avance promedio de sus metas.
                </div>

                <div class="grid">
                    @forelse($entidades as $e)
                        @php
                            $p = (int)$e->kpi_progreso;
                            $done = $p >= 100;
                        @endphp

                        <a href="{{ route('seguimiento.organizacion.entidad', $e->id) }}" class="card" style="text-decoration:none;">
                            <div class="flex justify-between items-start gap-3">
                                <div>
                                    <div class="title">{{ $e->nombre }}</div>
                                    <div class="muted" style="margin-top:6px;">
                                        {{ $e->descripcion ?? 'Sin descripción.' }}
                                    </div>
                                </div>

                                <span class="status-btn {{ $done ? 'done' : 'progressing' }}">
                                    <span class="pill-dot"></span>
                                    {{ $done ? 'Cumplida' : 'En progreso' }}
                                </span>
                            </div>

                            <div class="kpis">
                                <div class="kpi" style="background:#f0f4fb">
                                    <div class="label">Planes</div>
                                    <div class="value">{{ $e->kpi_planes }}</div>
                                </div>
                                <div class="kpi" style="background:#eef7f5">
                                    <div class="label">Metas</div>
                                    <div class="value">{{ $e->kpi_metas }}</div>
                                </div>
                                <div class="kpi">
                                    <div class="label">Programas</div>
                                    <div class="value">{{ $e->kpi_programas }}</div>
                                </div>
                                <div class="kpi">
                                    <div class="label">Proyectos</div>
                                    <div class="value">{{ $e->kpi_proyectos }}</div>
                                </div>
                            </div>

                            <div style="margin-top:14px;">
                                <div class="flex justify-between text-sm">
                                    <span class="muted">Avance promedio</span>
                                    <strong>{{ $p }}%</strong>
                                </div>
                                <div class="progress">
                                    <div style="width:{{ $p }}%; background:{{ $done ? 'var(--green)' : 'var(--orange)' }}"></div>
                                </div>
                            </div>

                            <div class="flex justify-end mt-4">
                                <span class="link">Ver detalle →</span>
                            </div>
                        </a>
                    @empty
                        <div class="card">
                            <div class="title">Sin entidades</div>
                            <div class="muted" style="margin-top:6px;">No hay entidades registradas.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
