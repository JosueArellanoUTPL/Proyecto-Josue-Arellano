<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Avance de Proyecto
        </h2>
    </x-slot>
    {{-- Los estilos reutilizables de esta vista ahora estan en resources/css/app.css --}}

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                {{-- Encabezado --}}
                <div class="row">
                    <div>
                        <div class="title">Editar avance</div>
                        <div class="muted" style="margin-top:6px;">
                            Proyecto: <strong>{{ $avance->proyecto->nombre }}</strong>
                        </div>
                        <div class="muted" style="margin-top:6px;">
                            Entidad: <strong>{{ $avance->proyecto->entidad->nombre ?? '—' }}</strong> ·
                            Programa: <strong>{{ $avance->proyecto->programa->nombre ?? '—' }}</strong>
                        </div>
                    </div>

                    <a class="btn" href="{{ route('seguimiento.proyecto.show', $avance->proyecto_id) }}">
                        ← Volver
                    </a>
                </div>

                {{-- Progreso actual del avance --}}
                @php
                    $p = max(0, min(100, (int)round($avance->porcentaje_avance)));
                    $done = $p >= 100;
                @endphp

                <div class="card" style="margin-top:16px;">
                    <div class="row" style="align-items:center;">
                        <div>
                            <div class="title">Porcentaje del avance</div>
                            <div class="muted" style="margin-top:6px;">Este porcentaje impacta el progreso actual del proyecto.</div>
                        </div>
                        <span class="badge {{ $done ? 'green' : 'orange' }}">{{ $p }}%</span>
                    </div>

                    <div class="progress">
                        <div style="width:{{ $p }}%; background:{{ $done ? 'var(--green)' : 'var(--orange)' }}"></div>
                    </div>
                </div>

                {{-- Form de edición --}}
                <div class="grid2">
                    <form class="card" method="POST" action="{{ route('proyectos.avance.update', $avance->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="title">Datos del avance</div>
                        <div class="muted" style="margin-top:6px;">Se actualiza fecha, porcentaje y comentario.</div>

                        @if ($errors->any())
                            <div style="margin-top:12px; padding:12px; border-radius:14px; border:1px solid #f3b4b4; background:#fff5f5;">
                                <div class="title">Revisar campos</div>
                                <ul class="muted" style="margin-top:8px; list-style:disc; padding-left:18px;">
                                    @foreach($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div style="margin-top:14px; display:grid; gap:12px;">
                            <div>
                                <label class="label">Fecha</label>
                                <input class="input" type="date" name="fecha"
                                       value="{{ old('fecha', $avance->fecha?->format('Y-m-d')) }}">
                            </div>

                            <div>
                                <label class="label">Porcentaje (0 a 100)</label>
                                <input class="input" type="number" step="0.01" min="0" max="100"
                                       name="porcentaje_avance"
                                       value="{{ old('porcentaje_avance', $avance->porcentaje_avance) }}">
                            </div>

                            <div>
                                <label class="label">Comentario</label>
                                <textarea class="input" name="comentario" rows="3"
                                          placeholder="Descripción breve del avance...">{{ old('comentario', $avance->comentario) }}</textarea>
                            </div>
                        </div>

                        <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
                            <button class="btn btn-green" type="submit">Actualizar avance</button>
                            <a class="btn" href="{{ route('seguimiento.proyecto.show', $avance->proyecto_id) }}">Cancelar</a>
                        </div>
                    </form>

                    {{-- Evidencias del avance --}}
                    <div class="card">
                        <div class="title">Evidencias</div>
                        <div class="muted" style="margin-top:6px;">
                            Se permite agregar evidencias en distintos momentos sin sobrescribir las anteriores.
                        </div>

                        {{-- Agregar evidencia (1 archivo por vez) --}}
                        <form method="POST"
                              action="{{ route('proyectos.avance.evidencia.add', $avance->id) }}"
                              enctype="multipart/form-data"
                              style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                            @csrf
                            <input class="input" style="max-width:360px;" type="file" name="evidencia" required>
                            <button class="btn" type="submit">+ Evidencia</button>
                        </form>

                        {{-- Galería --}}
                        @if($avance->evidencias->count())
                            <div class="evid-grid">
                                @foreach($avance->evidencias as $ev)
                                    @php
                                        $url = asset('storage/' . $ev->path);
                                        $isImg = $ev->mime_type && str_starts_with($ev->mime_type, 'image/');
                                        $label = $ev->original_name ?? 'Archivo';
                                    @endphp

                                    <div class="thumb">
                                        <a href="{{ $url }}" target="_blank" style="text-decoration:none; width:100%; height:100%;">
                                            @if($isImg)
                                                <img src="{{ $url }}" alt="{{ $label }}">
                                            @else
                                                <div class="filebox" style="height:100%;">
                                                    <div class="fileicon">📄</div>
                                                    <div class="fname">{{ \Illuminate\Support\Str::limit($label, 22) }}</div>
                                                </div>
                                            @endif
                                        </a>

                                        <form method="POST"
                                              action="{{ route('proyectos.avance.evidencia.delete', $ev->id) }}"
                                              onsubmit="return confirm('¿Eliminar esta evidencia?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="remove" type="submit">✕</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="muted" style="margin-top:12px;">Aún no hay evidencias cargadas para este avance.</div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
