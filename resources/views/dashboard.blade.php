<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Institucional
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                {{-- Encabezado del dashboard con accesos rapidos. --}}
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

                {{-- Tarjetas KPI: muestran conteos generales del sistema. --}}
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

                {{-- Graficas principales: donas y avance de alineacion. --}}
                <div class="dashboard-grid">
                    <div class="card dashboard-score">
                        <div>
                            <div class="title">Avance institucional</div>
                            <div class="muted" style="margin-top:6px;">
                                Promedio calculado desde metas e indicadores.
                            </div>
                        </div>

                        {{-- La variable --value alimenta la dona hecha con CSS. --}}
                        <div class="donut" style="--value: {{ $progresoInstitucional }};">
                            <div>
                                <strong>{{ $progresoInstitucional }}%</strong>
                                <span>metas</span>
                            </div>
                        </div>

                        <div class="dashboard-mini-grid">
                            <div class="mini-stat">
                                <span>Completadas</span>
                                <strong>{{ $metasCompletadas }}</strong>
                            </div>
                            <div class="mini-stat">
                                <span>En progreso</span>
                                <strong>{{ $metasEnProgreso }}</strong>
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

                    <div class="card">
                        <div class="title">Alineacion estrategica</div>
                        <div class="muted" style="margin-top:6px;">
                            Porcentaje de metas vinculadas a instrumentos estrategicos.
                        </div>

                        <div class="big-number">{{ $porcentajeAlineacion }}%</div>
                        <div class="progress">
                            <div style="width:{{ $porcentajeAlineacion }}%; background:var(--green)"></div>
                        </div>

                        <div class="dashboard-mini-grid" style="margin-top:14px;">
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

                {{-- Ranking por entidad y grafica mensual. --}}
                <div class="dashboard-grid-2">
                    <div class="card">
                        <div class="title">Avance por entidad</div>
                        <div class="muted" style="margin-top:6px;">
                            Ranking de entidades segun el progreso promedio de sus metas.
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
                        <div class="title">Actividad mensual</div>
                        <div class="muted" style="margin-top:6px;">
                            Avances de proyectos registrados en los ultimos 6 meses.
                        </div>

                        <div class="month-chart">
                            @foreach($actividadMensual as $mes)
                                @php
                                    // Altura visual de cada barra segun el mes con mayor actividad.
                                    $height = $maxActividadMensual > 0
                                        ? max(8, round(($mes['total'] / $maxActividadMensual) * 120))
                                        : 8;
                                @endphp
                                <div class="month-bar">
                                    <span>{{ $mes['total'] }}</span>
                                    <div style="height:{{ $height }}px"></div>
                                    <small>{{ $mes['label'] }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Ultimos avances registrados en proyectos. --}}
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
                        @forelse($actividadReciente as $a)
                            @php
                                // Normalizo el porcentaje para pintar la barra de cada tarjeta.
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
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
