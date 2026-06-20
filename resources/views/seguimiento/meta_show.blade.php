<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de Meta
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensaje que aparece despues de registrar/editar/eliminar un avance. --}}
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="wrap">

                {{-- Encabezado con datos principales de la meta. --}}
                <div class="flex justify-between flex-wrap gap-4 items-start">
                    <div>
                        <div class="title">{{ $meta->codigo }} - {{ $meta->nombre }}</div>
                        <div class="muted" style="margin-top:6px;">
                            Plan: {{ $meta->plan->codigo ?? '' }} - {{ $meta->plan->nombre ?? '' }}
                        </div>
                        <div class="muted" style="margin-top:6px;">
                            {{ $meta->descripcion ?? 'Sin descripcion.' }}
                        </div>
                    </div>

                    <a class="btn" href="{{ route('seguimiento.metas') }}">Volver</a>
                </div>

                @php
                    // Progreso calculado desde el modelo Meta.
                    $p = max(0, min(100, (float)$meta->progreso));
                    $done = (bool)$meta->completada;
                    $pending = $meta->indicadores->isEmpty();
                @endphp

                {{-- Barra general de progreso de la meta. --}}
                <div class="card" style="margin-top:16px;">
                    <div class="flex justify-between items-start gap-3">
                        <div>
                            <div class="title">Progreso de la Meta</div>
                            <div class="muted" style="margin-top:6px;">
                                Calculado automaticamente con el promedio de avance de los indicadores.
                            </div>
                        </div>
                        <strong>{{ $pending ? 'Pendiente de indicadores' : round($p).'%' }}</strong>
                    </div>

                    @if(!$pending)
                        <div class="progress">
                            <div style="width:{{ $p }}%; background:{{ $done ? 'var(--green)' : 'var(--orange)' }}"></div>
                        </div>
                    @endif
                </div>

                {{-- Proyectos que ayudan directamente al cumplimiento de esta meta. --}}
                <div class="title" style="margin-top:22px;">Proyectos relacionados</div>
                <div class="grid-ind">
                    @forelse($meta->proyectos as $proyecto)
                        <a class="card" href="{{ route('seguimiento.proyecto.show', $proyecto) }}">
                            <div class="font-semibold">{{ $proyecto->nombre }}</div>
                            <div class="muted" style="margin-top:6px;">
                                Avance actual: {{ round($proyecto->progreso, 2) }}%
                            </div>
                        </a>
                    @empty
                        <div class="card">
                            <div class="muted">Esta meta todavía no tiene proyectos relacionados.</div>
                        </div>
                    @endforelse
                </div>

                {{-- Lista de indicadores de esta meta. --}}
                <div class="title" style="margin-top:22px;">Indicadores</div>
                <div class="muted" style="margin-top:6px;">
                    Cada indicador puede registrar multiples avances con evidencias.
                </div>

                <div class="grid-ind">
                    @forelse($meta->indicadores as $ind)
                        @php
                            // Datos de avance de cada indicador.
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
                                        {{ $ind->descripcion ?? 'Sin descripcion.' }}
                                    </div>
                                </div>

                                <strong>{{ round($ip) }}%</strong>
                            </div>

                            <div class="progress">
                                <div style="width:{{ $ip }}%; background:{{ $idone ? 'var(--green)' : 'var(--orange)' }}"></div>
                            </div>

                            {{-- Ultimo avance registrado del indicador. --}}
                            <div class="muted" style="margin-top:12px;">
                                Ultimo avance:
                            </div>

                            @if($last)
                                <div class="flex gap-4 items-center mt-2 text-sm">
                                    @if($last->evidencia_path)
                                        <a class="link" href="{{ asset('storage/'.$last->evidencia_path) }}" target="_blank">
                                            Ver evidencia
                                        </a>
                                    @else
                                        <span class="muted">Sin evidencia</span>
                                    @endif

                                    {{-- Estos botones solo aparecen para admin o el usuario que creo el avance. --}}
                                    @if(auth()->id() === $last->user_id || auth()->user()->isAdmin())
                                        <a class="link" href="{{ route('indicadores.avance.edit', $last->id) }}">
                                            Editar
                                        </a>

                                        <form method="POST"
                                              action="{{ route('indicadores.avance.destroy', $last->id) }}"
                                              onsubmit="return confirm('Eliminar este avance?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="link">Eliminar</button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <div class="muted">Sin avances registrados</div>
                            @endif

                            {{-- Solo admin y tecnico pueden registrar avances. --}}
                            @if(auth()->user()->canRegisterSeguimiento())
                                <div class="mt-4">
                                    <a class="btn" href="{{ route('indicadores.avance.create', $ind->id) }}">
                                        Registrar avance
                                    </a>
                                </div>
                            @endif
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
