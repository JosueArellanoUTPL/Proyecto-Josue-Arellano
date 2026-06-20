<x-app-layout>
    {{-- Seccion de encabezado. --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Seguimiento de Programa
        </h2>
    </x-slot>

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

                    @if($programa->entidad_id)
                        <a class="btn" href="{{ route('seguimiento.organizacion.entidad', $programa->entidad_id) }}">
                            ← Volver a Entidad
                        </a>
                    @else
                        <a class="btn" href="{{ route('seguimiento.organizacion') }}">
                            ← Volver
                        </a>
                    @endif
                </div>

                @php $p = max(0, min(100, (int)$progresoPrograma)); @endphp

                <div class="card" style="margin-top:16px;">
                    <div class="row" style="align-items:center;">
                        <div>
                            <div class="title">Avance del Programa</div>
                            <div class="muted" style="margin-top:6px;">
                                Calculado como promedio del avance de sus proyectos.
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
                    <div class="muted" style="margin-top:6px;">Cada proyecto muestra su avance actual y permite ver el historial con evidencias.</div>

                    <div class="list">
                        @forelse($programa->proyectos as $pry)
                            @php
                                $pp = max(0, min(100, (int) round($pry->progreso ?? 0)));
                                $pdone = $pp >= 100;
                            @endphp

                            <a href="{{ route('seguimiento.proyecto.show', $pry->id) }}" style="text-decoration:none;">
                                <div class="click-card">
                                    <div style="display:flex; justify-content:space-between; gap:10px; align-items:flex-start;">
                                        <div>
                                            <strong style="color:var(--text)">{{ $pry->nombre }}</strong>
                                            <div class="muted" style="margin-top:4px;">
                                                {{ $pry->descripcion ? \Illuminate\Support\Str::limit($pry->descripcion, 80) : 'Sin descripción.' }}
                                            </div>
                                        </div>

                                        <div style="display:flex; gap:8px; align-items:center;">
                                            <span class="badge {{ $pry->activo ? 'green' : 'orange' }}">
                                                {{ $pry->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                            <span class="badge {{ $pdone ? 'green' : 'orange' }}">
                                                {{ $pp }}%
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mini">
                                        <div class="row2">
                                            <span class="muted">Avance actual</span>
                                            <span class="pct">{{ $pp }}%</span>
                                        </div>
                                        <div class="progress">
                                            <div style="width:{{ $pp }}%; background:{{ $pdone ? 'var(--green)' : 'var(--orange)' }}"></div>
                                        </div>
                                    </div>
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
