<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Organización (Entidades)
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">
                <div class="title">Mapa Organizacional</div>
                <div class="muted" style="margin-top:6px;">
                    Visualiza Entidades con sus Programas/Proyectos y el avance promedio de sus metas.
                </div>

                <div class="entity-grid">
                    @forelse($entidades as $entidad)
                        @php
                            $progresoEntidad = (int)$entidad->kpi_progreso;
                            $entidadCompletada = $progresoEntidad >= 100;
                        @endphp

                        <a href="{{ route('seguimiento.organizacion.entidad', $entidad->id) }}" class="card" style="text-decoration:none;">
                            <div class="flex justify-between items-start gap-3">
                                {{-- Datos de la entidad. --}}
                                <div>
                                    <div class="title">{{ $entidad->nombre }}</div>
                                    <div class="muted" style="margin-top:6px;">
                                        {{ $entidad->descripcion ?? 'Sin descripción.' }}
                                    </div>
                                </div>

                                <span class="status-btn {{ $entidadCompletada ? 'done' : 'progressing' }}">
                                    <span class="pill-dot"></span>
                                    {{ $entidadCompletada ? 'Cumplida' : 'En progreso' }}
                                </span>
                            </div>

                            <div class="kpis">
                                <div class="kpi" style="background:#f0f4fb">
                                    <div class="label">Planes</div>
                                    <div class="value">{{ $entidad->kpi_planes }}</div>
                                </div>
                                <div class="kpi" style="background:#eef7f5">
                                    <div class="label">Metas</div>
                                    <div class="value">{{ $entidad->kpi_metas }}</div>
                                </div>
                                <div class="kpi">
                                    <div class="label">Programas</div>
                                    <div class="value">{{ $entidad->kpi_programas }}</div>
                                </div>
                                <div class="kpi">
                                    <div class="label">Proyectos</div>
                                    <div class="value">{{ $entidad->kpi_proyectos }}</div>
                                </div>
                            </div>

                            {{-- Avance calculado. --}}
                            <div style="margin-top:14px;">
                                <div class="flex justify-between text-sm">
                                    <span class="muted">Avance promedio</span>
                                    <strong>{{ $progresoEntidad }}%</strong>
                                </div>
                                <div class="progress">
                                    <div style="width:{{ $progresoEntidad }}%; background:{{ $entidadCompletada ? 'var(--green)' : 'var(--orange)' }}"></div>
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
