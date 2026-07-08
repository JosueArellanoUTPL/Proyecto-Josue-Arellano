<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Institucional
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                {{-- Encabezado. --}}
                <div class="dashboard-hero">
                    <div>
                        <div class="title">Panel Ejecutivo</div>
                        <div class="muted" style="margin-top:6px;">
                            Vista resumida del avance institucional, trazabilidad y seguimiento de proyectos.
                        </div>
                    </div>

                    <div class="dashboard-hero-actions">
                        <a class="btn" href="{{ route('reportes.index') }}">Reportes</a>
                        <a class="btn" href="{{ route('seguimiento.trazabilidad') }}">Trazabilidad</a>
                    </div>
                </div>

                {{-- Conteos generales. --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" style="margin-top:18px;">
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
                        <div class="label">Proyectos</div>
                        <div class="value">{{ $kpis['proyectos'] ?? 0 }}</div>
                    </div>
                </div>

                {{-- Resumen de avance. --}}
                <div class="dashboard-grid">
                    <div class="card dashboard-score">
                        <div>
                            <div class="title">Avance institucional</div>
                            <div class="muted" style="margin-top:6px;">
                                Promedio calculado desde metas e indicadores.
                            </div>
                        </div>

                        <div class="donut" style="--value: {{ $progresoInstitucional }};">
                            <div>
                                <strong>{{ $progresoInstitucional }}%</strong>
                                <span>metas</span>
                            </div>
                        </div>

                        <div class="dashboard-mini-grid dashboard-mini-grid-3">
                            <div class="mini-stat">
                                <span>Completadas</span>
                                <strong>{{ $metasCompletadas }}</strong>
                            </div>
                            <div class="mini-stat">
                                <span>En progreso</span>
                                <strong>{{ $metasEnProgreso }}</strong>
                            </div>
                            <div class="mini-stat">
                                <span>Pendientes</span>
                                <strong>{{ $metasPendientes }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="card dashboard-score">
                        <div>
                            <div class="title">Proyectos</div>
                            <div class="muted" style="margin-top:6px;">
                                Avance promedio del ultimo seguimiento registrado.
                            </div>
                        </div>

                        <div class="donut donut-blue" style="--value: {{ $progresoProyectos }};">
                            <div>
                                <strong>{{ $progresoProyectos }}%</strong>
                                <span>avance</span>
                            </div>
                        </div>

                        <div class="dashboard-mini-grid">
                            <div class="mini-stat">
                                <span>Completados</span>
                                <strong>{{ $proyectosCompletados }}</strong>
                            </div>
                            <div class="mini-stat">
                                <span>En proceso</span>
                                <strong>{{ $proyectosEnProgreso }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="card dashboard-score">
                        <div>
                            <div class="title">Alineacion estrategica</div>
                            <div class="muted" style="margin-top:6px;">
                                Metas vinculadas a instrumentos estrategicos.
                            </div>
                        </div>

                        <div class="donut" style="--value: {{ $porcentajeAlineacion }};">
                            <div>
                                <strong>{{ $porcentajeAlineacion }}%</strong>
                                <span>alineacion</span>
                            </div>
                        </div>

                        <div class="dashboard-mini-grid">
                            <div class="mini-stat">
                                <span>Alineadas</span>
                                <strong>{{ $metasAlineadas }}</strong>
                            </div>
                            <div class="mini-stat">
                                <span>No alineadas</span>
                                <strong>{{ $metasNoAlineadas }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Avance por entidad y estado de metas. --}}
                <div class="dashboard-grid-2">
                    <div class="card">
                        <div class="title">Avance por entidad</div>
                        <div class="muted" style="margin-top:6px;">
                            Ranking de entidades según el progreso promedio de sus metas.
                        </div>

                        <div class="bar-list">
                            @forelse($avancePorEntidad as $entidad)
                                <div class="bar-row">
                                    <div class="bar-row-head">
                                        <span>{{ $entidad['nombre'] }}</span>
                                        <strong>{{ $entidad['progreso'] }}%</strong>
                                    </div>
                                    <div class="progress">
                                        <div style="width:{{ $entidad['progreso'] }}%; background:var(--blue)"></div>
                                    </div>
                                    <div class="muted" style="margin-top:4px;">
                                        Metas asociadas: {{ $entidad['total_metas'] }}
                                    </div>
                                </div>
                            @empty
                                <div class="muted">No hay entidades con metas registradas.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card">
                        <div class="title">Distribucion de metas</div>
                        <div class="muted" style="margin-top:6px;">
                            Cantidad de metas según su estado actual.
                        </div>

                        <div class="bar-list">
                            @foreach($distribucionMetas as $estado)
                                <div class="bar-row">
                                    <div class="bar-row-head">
                                        <span>{{ $estado['label'] }}</span>
                                        <strong>{{ $estado['total'] }} ({{ $estado['porcentaje'] }}%)</strong>
                                    </div>
                                    <div class="progress">
                                        <div style="width:{{ $estado['porcentaje'] }}%; background:{{ $estado['color'] }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Actividad reciente. --}}
                <div class="card mt-6">
                    <div class="row">
                        <div>
                            <div class="title">Actividad reciente</div>
                            <div class="muted" style="margin-top:6px;">
                                Ultimos avances registrados en proyectos.
                            </div>
                        </div>
                        <a class="btn" href="{{ route('reportes.proyectos') }}">Ver reporte</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                        @forelse($actividadReciente as $actividad)
                            @php
                                // Normalizo el porcentaje para pintar la barra de cada tarjeta.
                                $porcentajeActividad = max(0, min(100, (int)round((float)$actividad->porcentaje_avance)));
                                $actividadCompletada = $porcentajeActividad >= 100;
                            @endphp

                            <div class="kpi">
                                <div class="label">
                                    {{ $actividad->proyecto->nombre ?? 'Proyecto' }}
                                </div>
                                <div class="value">{{ $porcentajeActividad }}%</div>
                                <div class="muted" style="margin-top:4px;">
                                    {{ $actividad->fecha?->format('d/m/Y') ?? '-' }}
                                </div>
                                <div class="progress">
                                    <div style="width:{{ $porcentajeActividad }}%; background:{{ $actividadCompletada ? 'var(--green)' : 'var(--orange)' }}"></div>
                                </div>
                            </div>
                        @empty
                            <div class="muted">No hay actividad reciente.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
