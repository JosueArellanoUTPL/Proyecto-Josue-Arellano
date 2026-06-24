<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de Entidad
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                <div class="flex justify-between flex-wrap gap-4 items-start">
                    <div>
                        <div class="title">{{ $entidad->nombre }}</div>
                        <div class="muted" style="margin-top:6px;">
                            {{ $entidad->descripcion ?? 'Sin descripción.' }}
                        </div>
                    </div>

                    <a class="btn" href="{{ route('seguimiento.organizacion') }}">← Volver</a>
                </div>

                @php $porcentajeEntidad = max(0, min(100, (int)$progresoEntidad)); @endphp

                <div class="card" style="margin-top:16px;">
                    <div class="row">
                        <div>
                            <div class="title">Avance promedio de metas</div>
                            <div class="muted" style="margin-top:6px;">
                                Promedio calculado con el progreso de metas asociadas a los planes de esta entidad.
                            </div>
                        </div>
                        <div class="badge {{ $porcentajeEntidad >= 100 ? 'green' : 'orange' }}">
                            {{ $porcentajeEntidad }}%
                        </div>
                    </div>

                    <div class="progress">
                        <div style="width:{{ $porcentajeEntidad }}%; background:{{ $porcentajeEntidad >= 100 ? 'var(--green)' : 'var(--orange)' }}"></div>
                    </div>
                </div>

                <div class="grid2">

                    {{-- Programas y proyectos. --}}
                    <div class="card">
                        <div class="title">Programas</div>
                        <div class="muted" style="margin-top:6px;">Programas asociados a esta entidad.</div>

                        <div class="list">
                            @forelse($entidad->programas as $prog)
                                <a href="{{ route('seguimiento.programa.show', $prog->id) }}" style="text-decoration:none;">
                                    <div class="row click-card">
                                        <div>
                                            <strong style="color:var(--text)">{{ $prog->nombre }}</strong>
                                            <div class="muted" style="margin-top:4px;">
                                                {{ $prog->descripcion ? \Illuminate\Support\Str::limit($prog->descripcion, 80) : 'Sin descripción.' }}
                                            </div>
                                        </div>
                                        <span class="badge {{ $prog->activo ? 'green' : 'orange' }}">
                                            {{ $prog->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div class="muted">No hay programas.</div>
                            @endforelse
                        </div>

                        <hr style="margin:14px 0; border-color:var(--border-soft);" />

                        <div class="title">Proyectos</div>
                        <div class="muted" style="margin-top:6px;">Proyectos asociados.</div>

                        <div class="list">
                            @forelse($entidad->proyectos as $proyecto)
                                <a href="{{ route('seguimiento.proyecto.show', $proyecto->id) }}" style="text-decoration:none;">
                                    <div class="row click-card">
                                        <div>
                                            <strong style="color:var(--text)">{{ $proyecto->nombre }}</strong>
                                            <div class="muted" style="margin-top:4px;">
                                                Programa: {{ $proyecto->programa->nombre ?? '-' }}
                                            </div>
                                        </div>
                                        <span class="badge {{ $proyecto->activo ? 'green' : 'orange' }}">
                                            {{ $proyecto->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div class="muted">No hay proyectos.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Planes y metas. --}}
                    <div class="card">
                        <div class="title">Planes y Metas</div>
                        <div class="muted" style="margin-top:6px;">Metas asociadas a los planes de esta entidad.</div>

                        <div class="list">
                            @forelse($entidad->planes as $plan)
                                <div style="padding:12px; border-radius:14px; border:1px solid var(--border-soft); background:#fafafa;">
                                    <div class="row">
                                        <strong>{{ $plan->codigo }} — {{ $plan->nombre }}</strong>
                                        <span class="muted">{{ $plan->pdn->codigo ?? '' }}</span>
                                    </div>

                                    @php
                                        $metasPlan = $plan->metas ?? collect();
                                    @endphp

                                    <div class="muted" style="margin-top:8px;">
                                        Metas: <strong>{{ $metasPlan->count() }}</strong>
                                    </div>

                                    <div style="margin-top:10px; display:grid; gap:8px;">
                                        @foreach($metasPlan->take(3) as $meta)
                                            @php
                                                $progresoMeta = max(0, min(100, (float)$meta->progreso));
                                                $metaCompletada = (bool)$meta->completada;
                                            @endphp
                                            <a href="{{ route('seguimiento.meta.show', $meta->id) }}" style="text-decoration:none;">
                                                <div class="row">
                                                    <span class="muted">{{ $meta->codigo }} — {{ $meta->nombre }}</span>
                                                    <span class="badge {{ $metaCompletada ? 'green' : 'orange' }}">
                                                        {{ round($progresoMeta) }}%
                                                    </span>
                                                </div>
                                                <div class="progress" style="margin-top:6px;">
                                                    <div style="width:{{ $progresoMeta }}%; background:{{ $metaCompletada ? 'var(--green)' : 'var(--orange)' }}"></div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="muted">No hay planes asociados a esta entidad.</div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
