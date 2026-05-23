<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Institucional
        </h2>
    </x-slot>

    {{-- Los estilos reutilizables de esta vista ahora estan en resources/css/app.css --}}
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                {{-- KPIs superiores (compactos) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="kpi" style="background:#eef7f5">
                        <div class="label">Planes activos</div>
                        <div class="value">{{ $kpis['planes_activos'] ?? 0 }}</div>
                    </div>

                    <div class="kpi" style="background:#f0f4fb">
                        <div class="label">Metas</div>
                        <div class="value">{{ $kpis['metas'] ?? 0 }}</div>
                    </div>

                    <div class="kpi" style="background:#fff6ee">
                        <div class="label">Indicadores</div>
                        <div class="value">{{ $kpis['indicadores'] ?? 0 }}</div>
                    </div>

                    <div class="kpi" style="background:#f4f1fb">
                        <div class="label">Alineaciones</div>
                        <div class="value">{{ $kpis['alineaciones'] ?? 0 }}</div>
                    </div>
                </div>

                {{-- Bloque central (3 columnas) --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6">

                    {{-- Progreso institucional --}}
                    <div class="card">
                        <div class="title">Progreso institucional</div>
                        <div class="muted" style="margin-top:6px;">
                            Promedio del avance de metas (calculado por indicadores).
                        </div>

                        @php
                            $p = max(0, min(100, (int)$progresoInstitucional));
                            $done = $p >= 100;
                        @endphp

                        <div style="margin-top:14px;">
                            <div class="flex justify-between text-sm">
                                <span class="muted">Avance</span>
                                <strong>{{ $p }}%</strong>
                            </div>
                            <div class="progress">
                                <div style="width:{{ $p }}%; background:{{ $done ? 'var(--green)' : 'var(--blue)' }}"></div>
                            </div>
                        </div>

                        <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
                            <span class="pill"><span class="dot blue"></span>Indicadores</span>
                            <span class="pill"><span class="dot green"></span>Metas</span>
                        </div>
                    </div>

                    {{-- Estado de alineación --}}
                    <div class="card">
                        <div class="title">Alineación estratégica</div>
                        <div class="muted" style="margin-top:6px;">
                            Control de trazabilidad (metas alineadas vs no alineadas).
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div class="kpi" style="background:#eef7f5">
                                <div class="label">Alineadas</div>
                                <div class="value">{{ $metasAlineadas ?? 0 }}</div>
                            </div>

                            <div class="kpi" style="background:#fff6ee">
                                <div class="label">No alineadas</div>
                                <div class="value">{{ $metasNoAlineadas ?? 0 }}</div>
                            </div>
                        </div>

                        <div class="muted" style="margin-top:10px;">
                            Las alineaciones alimentan la matriz de trazabilidad institucional.
                        </div>
                    </div>

                    {{-- Accesos rápidos (con color más fuerte) --}}
                    <div class="card" style="background:#eef2ff; border-color:#d5d9ff;">
                        <div class="title">Accesos rápidos</div>
                        <div class="muted" style="margin-top:6px;">
                            Vistas principales para seguimiento y control.
                        </div>

                        <div class="flex flex-col gap-3 mt-4">
                            <a class="btn" href="{{ route('seguimiento.metas') }}">
                                Seguimiento de Metas →
                            </a>

                            <a class="btn" href="{{ route('seguimiento.organizacion') }}">
                                Organización →
                            </a>

                            <a class="btn" href="{{ route('seguimiento.trazabilidad') }}">
                                Matriz de Trazabilidad →
                            </a>
                        </div>

                        <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
                            <span class="pill"><span class="dot purple"></span>Control estratégico</span>
                            <span class="pill"><span class="dot orange"></span>Ejecución</span>
                        </div>
                    </div>

                </div>

                {{-- Actividad reciente (compacta, sin errores de arrays) --}}
                <div class="card mt-6">
                    <div class="title">Actividad reciente</div>
                    <div class="muted" style="margin-top:6px;">
                        Últimos avances registrados en proyectos.
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                        @forelse($actividadReciente as $a)
                            @php
                                $pp = max(0, min(100, (int)round((float)$a->porcentaje_avance)));
                                $pdone = $pp >= 100;
                            @endphp

                            <div class="kpi">
                                <div class="label">
                                    {{ $a->proyecto->nombre ?? 'Proyecto' }}
                                </div>
                                <div class="value">{{ $pp }}%</div>
                                <div class="muted" style="margin-top:4px;">
                                    {{ $a->fecha?->format('d/m/Y') ?? '-' }}
                                </div>
                                <div class="progress">
                                    <div style="width:{{ $pp }}%; background:{{ $pdone ? 'var(--green)' : 'var(--orange)' }}"></div>
                                </div>
                            </div>
                        @empty
                            <div class="muted">No hay actividad reciente.</div>
                        @endforelse
                    </div>

                    <div class="muted" style="margin-top:12px;">
                        La actividad reciente se alimenta desde los avances de proyectos con evidencias.
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
