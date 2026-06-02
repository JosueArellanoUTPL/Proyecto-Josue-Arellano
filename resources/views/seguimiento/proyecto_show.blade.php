<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Seguimiento de Proyecto
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                {{-- Datos principales del proyecto y boton para registrar avance. --}}
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
                        {{-- Solo admin y tecnico pueden registrar avances. --}}
                        @if(auth()->user()->canRegisterSeguimiento())
                            <a class="btn" href="{{ route('proyectos.avance.create', $proyecto->id) }}" style="background:#eef7f5;">
                                + Registrar avance
                            </a>
                        @endif
                    </div>
                </div>

                @php
                    // Progreso actual del proyecto, limitado entre 0 y 100.
                    $p = max(0, min(100, (int)$progresoProyecto));
                @endphp

                {{-- Barra de avance actual del proyecto. --}}
                <div class="card" style="margin-top:16px;">
                    <div class="row" style="align-items:center;">
                        <div class="title">Avance actual</div>
                        <span class="badge {{ $p >= 100 ? 'green' : 'orange' }}">{{ $p }}%</span>
                    </div>
                    <div class="progress">
                        <div style="width:{{ $p }}%; background:{{ $p >= 100 ? 'var(--green)' : 'var(--orange)' }}"></div>
                    </div>
                </div>

                {{-- Historial completo de avances del proyecto. --}}
                <div class="card" style="margin-top:14px;">
                    <div class="title">Historial de avances</div>

                    <div class="project-history-grid">
                        @foreach($proyecto->avances as $a)
                            <div class="card" style="background:#fafafa;">
                                <div class="row">
                                    <div>
                                        <strong>{{ $a->porcentaje_avance }}%</strong>
                                        <span class="muted">- {{ $a->fecha->format('d/m/Y') }}</span>
                                    </div>

                                    {{-- Editar/eliminar solo para admin o el usuario que creo el avance. --}}
                                    @if(auth()->user()->isAdmin() || auth()->id() === $a->user_id)
                                        <div style="display:flex; gap:10px;">
                                            <a class="btn" href="{{ route('proyectos.avance.edit', $a->id) }}">Editar</a>
                                            <form method="POST" action="{{ route('proyectos.avance.destroy', $a->id) }}">
                                                @csrf @method('DELETE')
                                                <button class="btn" onclick="return confirm('Eliminar avance?')">Eliminar</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                {{-- Evidencias asociadas a este avance. --}}
                                <div class="evid-grid">
                                    @foreach($a->evidencias as $ev)
                                        <div class="thumb">
                                            <img src="{{ asset('storage/'.$ev->path) }}">
                                            @if(auth()->user()->isAdmin() || auth()->id() === $a->user_id)
                                                <form method="POST" action="{{ route('proyectos.avance.evidencia.delete', $ev->id) }}">
                                                    @csrf @method('DELETE')
                                                    <button class="remove">x</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Permite agregar mas evidencia sin borrar la anterior. --}}
                                @if(auth()->user()->isAdmin() || auth()->id() === $a->user_id)
                                    <form method="POST" enctype="multipart/form-data"
                                          action="{{ route('proyectos.avance.evidencia.add', $a->id) }}"
                                          style="margin-top:10px;">
                                        @csrf
                                        <input type="file" name="evidencia" required>
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
