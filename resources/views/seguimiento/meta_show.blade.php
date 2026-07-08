<x-app-layout>
    {{-- Encabezado con la meta seleccionada. --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Meta: {{ $meta->nombre }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-success-message />

            <div class="wrap">

                {{-- Datos de la meta. --}}
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
                    $progresoMeta = max(0, min(100, (float)$meta->progreso));
                    $metaCompletada = (bool)$meta->completada;
                    $sinIndicadores = $meta->indicadores->isEmpty();
                @endphp

                {{-- Progreso de la meta. --}}
                <div class="card" style="margin-top:16px;">
                    <div class="flex justify-between items-start gap-3">
                        <div>
                            <div class="title">Progreso de la Meta</div>
                            <div class="muted" style="margin-top:6px;">
                                Calculado automaticamente con el promedio de avance de los indicadores.
                            </div>
                        </div>
                        <strong>{{ $sinIndicadores ? 'Pendiente de indicadores' : round($progresoMeta).'%' }}</strong>
                    </div>

                    @if(!$sinIndicadores)
                        <div class="progress">
                            <div style="width:{{ $progresoMeta }}%; background:{{ $metaCompletada ? 'var(--green)' : 'var(--orange)' }}"></div>
                        </div>
                    @endif
                </div>


                {{-- Indicadores. --}}
                <div class="title" style="margin-top:22px;">Indicadores</div>
                <div class="muted" style="margin-top:6px;">
                    Cada indicador puede registrar multiples avances con evidencias.
                </div>

                <div class="grid-ind">
                    @forelse($meta->indicadores as $indicador)
                        @php
                            // Datos de avance de cada indicador.
                            $progresoIndicador = max(0, min(100, (float)$indicador->progreso));
                            $indicadorCompletado = (bool)$indicador->completado;
                            $ultimoAvance = $indicador->ultimoAvance;
                        @endphp

                        <div class="card">
                            <div class="flex justify-between items-start gap-3">
                                <div>
                                    <div class="muted">{{ $indicador->codigo }}</div>
                                    <div class="font-semibold mt-1">{{ $indicador->nombre }}</div>
                                    <div class="muted" style="margin-top:6px;">
                                        {{ $indicador->descripcion ?? 'Sin descripcion.' }}
                                    </div>
                                </div>

                                <strong>{{ round($progresoIndicador) }}%</strong>
                            </div>

                            <div class="progress">
                                <div style="width:{{ $progresoIndicador }}%; background:{{ $indicadorCompletado ? 'var(--green)' : 'var(--orange)' }}"></div>
                            </div>

                            {{-- Ultimo avance. --}}
                            <div class="muted" style="margin-top:12px;">
                                Ultimo avance:
                            </div>

                            @if($ultimoAvance)
                                <div class="flex gap-4 items-center mt-2 text-sm">
                                    @if($ultimoAvance->evidencia_path)
                                        <a class="link" href="{{ asset('storage/'.$ultimoAvance->evidencia_path) }}" target="_blank">
                                            Ver evidencia
                                        </a>
                                    @else
                                        <span class="muted">Sin evidencia</span>
                                    @endif

                                    {{-- Permisos del avance. --}}
                                    @if(auth()->id() === $ultimoAvance->user_id || auth()->user()->isAdmin())
                                        <a class="link" href="{{ route('indicadores.avance.edit', $ultimoAvance->id) }}">
                                            Editar
                                        </a>

                                        <form method="POST"
                                              action="{{ route('indicadores.avance.destroy', $ultimoAvance->id) }}"
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

                            {{-- Permiso de seguimiento. --}}
                            @if(auth()->user()->canRegisterSeguimiento())
                                <div class="mt-4">
                                    <a class="btn" href="{{ route('indicadores.avance.create', $indicador->id) }}">
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
