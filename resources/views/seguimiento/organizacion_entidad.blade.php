<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de Entidad
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

        .progress{ width:100%; height:8px; background:#e5e7eb; border-radius:999px; overflow:hidden; margin-top:8px; }
        .progress div{ height:100%; }

        .grid2{ display:grid; grid-template-columns:1fr; gap:14px; margin-top:16px; }
        @media(min-width:1024px){ .grid2{ grid-template-columns: 1fr 1fr; } }

        .list{ margin-top:10px; display:grid; gap:10px; }
        .row{ display:flex; justify-content:space-between; gap:10px; align-items:center; }

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

        .click-card{
            padding:10px;
            border-radius:14px;
            border:1px solid var(--border-soft);
            background:#fafafa;
            transition: all .15s ease-in-out;
        }
        .click-card:hover{
            transform: translateY(-1px);
            background:#f6f8fb;
            border-color: var(--border);
        }
    </style>

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

                @php $p = max(0, min(100, (int)$progresoEntidad)); @endphp

                <div class="card" style="margin-top:16px;">
                    <div class="row">
                        <div>
                            <div class="title">Avance promedio de metas</div>
                            <div class="muted" style="margin-top:6px;">
                                Promedio calculado con el progreso de metas asociadas a los planes de esta entidad.
                            </div>
                        </div>
                        <div class="badge {{ $p >= 100 ? 'green' : 'orange' }}">
                            {{ $p }}%
                        </div>
                    </div>

                    <div class="progress">
                        <div style="width:{{ $p }}%; background:{{ $p >= 100 ? 'var(--green)' : 'var(--orange)' }}"></div>
                    </div>
                </div>

                <div class="grid2">

                    {{-- Programas y Proyectos --}}
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
                            @forelse($entidad->proyectos as $pry)
                                <a href="{{ route('seguimiento.proyecto.show', $pry->id) }}" style="text-decoration:none;">
                                    <div class="row click-card">
                                        <div>
                                            <strong style="color:var(--text)">{{ $pry->nombre }}</strong>
                                            <div class="muted" style="margin-top:4px;">
                                                Programa: {{ $pry->programa->nombre ?? '-' }}
                                            </div>
                                        </div>
                                        <span class="badge {{ $pry->activo ? 'green' : 'orange' }}">
                                            {{ $pry->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div class="muted">No hay proyectos.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Planes y Metas --}}
                    <div class="card">
                        <div class="title">Planes y Metas</div>
                        <div class="muted" style="margin-top:6px;">Metas asociadas a los planes de esta entidad.</div>

                        <div class="list">
                            @forelse($entidad->plans as $plan)
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
                                        @foreach($metasPlan->take(3) as $m)
                                            @php
                                                $mp = max(0, min(100, (float)$m->progreso));
                                                $md = (bool)$m->completada;
                                            @endphp
                                            <a href="{{ route('seguimiento.meta.show', $m->id) }}" style="text-decoration:none;">
                                                <div class="row">
                                                    <span class="muted">{{ $m->codigo }} — {{ $m->nombre }}</span>
                                                    <span class="badge {{ $md ? 'green' : 'orange' }}">
                                                        {{ round($mp) }}%
                                                    </span>
                                                </div>
                                                <div class="progress" style="margin-top:6px;">
                                                    <div style="width:{{ $mp }}%; background:{{ $md ? 'var(--green)' : 'var(--orange)' }}"></div>
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
