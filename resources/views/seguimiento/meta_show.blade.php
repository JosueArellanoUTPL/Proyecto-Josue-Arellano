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

        .wrap{ background:var(--beige); border-radius:20px; padding:28px; border:1px solid var(--border-soft); }
        .card{ background:var(--card); border-radius:18px; padding:18px; border:1px solid var(--border); }
        .title{ font-weight:700; font-size:18px; color:var(--text); }
        .muted{ color:var(--muted); font-size:13px; }

        .progress{ width:100%; height:8px; background:#e5e7eb; border-radius:999px; overflow:hidden; margin-top:8px; }
        .progress div{ height:100%; }

        .btn{
            display:inline-flex; align-items:center; justify-content:center;
            height:34px; padding:0 14px; border-radius:10px;
            border:1px solid var(--border);
            font-weight:700; font-size:13px;
            background:#f0f4fb; color:#365a99;
            text-decoration:none;
        }

        .link{ color:#6b86c9; font-weight:700; text-decoration:none; }
        .link:hover{ color:#5a76be; }

        .grid-ind{ display:grid; grid-template-columns:1fr; gap:14px; margin-top:18px; }
        @media(min-width:768px){ .grid-ind{ grid-template-columns:1fr 1fr; } }
    </style>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="wrap">

                {{-- Encabezado de la meta --}}
                <div class="flex justify-between flex-wrap gap-4 items-start">
                    <div>
                        <div class="title">{{ $meta->codigo }} — {{ $meta->nombre }}</div>
                        <div class="muted" style="margin-top:6px;">
                            Plan: {{ $meta->plan->codigo ?? '' }} — {{ $meta->plan->nombre ?? '' }}
                        </div>
                        <div class="muted" style="margin-top:6px;">
                            {{ $meta->descripcion ?? 'Sin descripción.' }}
                        </div>
                    </div>

                    <a class="btn" href="{{ route('seguimiento.metas') }}">← Volver</a>
                </div>

                {{-- Progreso de la meta --}}
                @php
                    $p = max(0, min(100, (float)$meta->progreso));
                    $done = (bool)$meta->completada;
                @endphp

                <div class="card" style="margin-top:16px;">
                    <div class="flex justify-between items-start gap-3">
                        <div>
                            <div class="title">Progreso de la Meta</div>
                            <div class="muted" style="margin-top:6px;">
                                Calculado automáticamente con el promedio de avance de los indicadores.
                            </div>
                        </div>
                        <strong>{{ round($p) }}%</strong>
                    </div>

                    <div class="progress">
                        <div style="width:{{ $p }}%; background:{{ $done ? 'var(--green)' : 'var(--orange)' }}"></div>
                    </div>
                </div>

                {{-- Indicadores --}}
                <div class="title" style="margin-top:22px;">Indicadores</div>
                <div class="muted" style="margin-top:6px;">
                    Cada indicador puede registrar múltiples avances con evidencias.
                </div>

                <div class="grid-ind">
                    @forelse($meta->indicadores as $ind)
                        @php
                            $ip = max(0, min(100, (float)$ind->progreso));
                            $idone = (bool)$ind->completado;
                            $last = $ind->ultimoAvance;
                        @endphp

                        <div class="card">
                            <div class="flex justify-between items-start gap-3">
                                <div>
                                    <div class="muted">{{ $ind->codigo }}</div>
                                    <div class="font-semibold mt-1">{{ $ind->nombre }}</div>
                                    <div class="muted" style="margin-top:6px;">
                                        {{ $ind->descripcion ?? 'Sin descripción.' }}
                                    </div>
                                </div>

                                <strong>{{ round($ip) }}%</strong>
                            </div>

                            <div class="progress">
                                <div style="width:{{ $ip }}%; background:{{ $idone ? 'var(--green)' : 'var(--orange)' }}"></div>
                            </div>

                            {{-- Último avance --}}
                            <div class="muted" style="margin-top:12px;">
                                Último avance:
                            </div>

                            @if($last)
                                <div class="flex gap-4 items-center mt-2 text-sm">
                                    @if($last->evidencia_path)
                                        <a class="link" href="{{ asset('storage/'.$last->evidencia_path) }}" target="_blank">
                                            Ver evidencia →
                                        </a>
                                    @else
                                        <span class="muted">Sin evidencia</span>
                                    @endif

                                    {{-- Edición y eliminación --}}
                                    @if(auth()->id() === $last->user_id || auth()->user()->role === 'admin')
                                        <a class="link" href="{{ route('indicadores.avance.edit', $last->id) }}">
                                            Editar
                                        </a>

                                        <form method="POST"
                                              action="{{ route('indicadores.avance.destroy', $last->id) }}"
                                              onsubmit="return confirm('¿Eliminar este avance?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="link">Eliminar</button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <div class="muted">Sin avances registrados</div>
                            @endif

                            <div class="mt-4">
                                <a class="btn" href="{{ route('indicadores.avance.create', $ind->id) }}">
                                    Registrar avance
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="card">
                            <div class="muted">No hay indicadores registrados para esta meta.</div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
