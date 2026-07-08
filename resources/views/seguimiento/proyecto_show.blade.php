<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Seguimiento de Proyecto
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                {{-- Datos del proyecto. --}}
                <div class="row">
                    <div>
                        <div class="title">{{ $proyecto->nombre }}</div>
                        <div class="muted" style="margin-top:6px;">
                            Entidad: <strong>{{ $proyecto->entidad->nombre ?? '-' }}</strong> -
                            Programa: <strong>{{ $proyecto->programa->nombre ?? '-' }}</strong>
                        </div>
                        <div class="muted" style="margin-top:6px;">
                            {{ $proyecto->descripcion ?? 'Sin descripcion.' }}
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a class="btn" href="{{ route('seguimiento.programa.show', $proyecto->programa_id) }}">Volver</a>
                        {{-- Permiso de seguimiento. --}}
                        @if(auth()->user()->canRegisterSeguimiento())
                            <a class="btn" href="{{ route('proyectos.avance.create', $proyecto->id) }}" style="background:#eef7f5;">
                                + Registrar avance
                            </a>
                        @endif
                    </div>
                </div>

                @php
                    // Progreso actual del proyecto, limitado entre 0 y 100.
                    $porcentajeProyecto = max(0, min(100, (int)$progresoProyecto));
                @endphp

                {{-- Progreso actual. --}}
                <div class="card" style="margin-top:16px;">
                    <div class="row" style="align-items:center;">
                        <div class="title">Avance actual</div>
                        <span class="badge {{ $porcentajeProyecto >= 100 ? 'green' : 'orange' }}">{{ $porcentajeProyecto }}%</span>
                    </div>
                    <div class="progress">
                        <div style="width:{{ $porcentajeProyecto }}%; background:{{ $porcentajeProyecto >= 100 ? 'var(--green)' : 'var(--orange)' }}"></div>
                    </div>
                </div>

                {{-- Historial de avances. --}}
                <div class="card" style="margin-top:14px;">
                    <div class="title">Historial de avances</div>

                    <div class="project-history-grid">
                        @foreach($proyecto->avances as $avance)
                            <div class="card" style="background:#fafafa;">
                                <div class="row">
                                    <div>
                                        <strong>{{ $avance->porcentaje_avance }}%</strong>
                                        <span class="muted">- {{ $avance->fecha->format('d/m/Y') }}</span>
                                    </div>

                                    {{-- Permisos del avance. --}}
                                    @if(auth()->user()->isAdmin() || auth()->id() === $avance->user_id)
                                        <div style="display:flex; gap:10px;">
                                            <a class="btn" href="{{ route('proyectos.avance.edit', $avance->id) }}">Editar</a>
                                            <form method="POST" action="{{ route('proyectos.avance.destroy', $avance->id) }}">
                                                @csrf @method('DELETE')
                                                <button class="btn" onclick="return confirm('Eliminar avance?')">Eliminar</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                {{-- Evidencias. --}}
                                <div class="evid-grid">
                                    @foreach($avance->evidencias as $evidencia)
                                        <div class="thumb">
                                            @if($evidencia->isImage())
                                                <img src="{{ asset('storage/'.$evidencia->path) }}" alt="{{ $evidencia->original_name ?? 'Evidencia' }}">
                                            @else
                                                <a class="link" href="{{ asset('storage/'.$evidencia->path) }}" target="_blank">
                                                    {{ $evidencia->original_name ?? 'Ver PDF' }}
                                                </a>
                                            @endif
                                            @if(auth()->user()->isAdmin() || auth()->id() === $avance->user_id)
                                                <form method="POST" action="{{ route('proyectos.avance.evidencia.delete', $evidencia->id) }}">
                                                    @csrf @method('DELETE')
                                                    <button class="remove">x</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Nueva evidencia. --}}
                                @if(auth()->user()->isAdmin() || auth()->id() === $avance->user_id)
                                    <form method="POST" enctype="multipart/form-data"
                                          action="{{ route('proyectos.avance.evidencia.add', $avance->id) }}"
                                          style="margin-top:10px;">
                                        @csrf
                                        <input type="file" name="evidencia" accept=".pdf,.jpg,.jpeg,.png" required>
                                        <button class="btn" style="margin-left:8px;">+ Evidencia</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
