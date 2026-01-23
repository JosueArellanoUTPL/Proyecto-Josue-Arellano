<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Avance de Proyecto
        </h2>
    </x-slot>

    <style>
        :root{
            --beige:#f5f1ea; --card:#ffffff; --border:#cfd5dd; --border-soft:#d9dee6;
            --blue:#8faadc; --green:#9fd3c7; --orange:#f4c095;
            --text:#1f2937; --muted:#6b7280;
        }
        .wrap{ background:var(--beige); border-radius:20px; padding:28px; border:1px solid var(--border-soft); }
        .card{ background:var(--card); border-radius:18px; padding:18px; border:1px solid var(--border); }
        .title{ font-weight:700; font-size:18px; color:var(--text); }
        .muted{ color:var(--muted); font-size:13px; }
        .row{ display:flex; justify-content:space-between; gap:10px; align-items:flex-start; flex-wrap:wrap; }

        .btn{
            display:inline-flex; align-items:center; justify-content:center;
            height:34px; padding:0 14px; border-radius:10px;
            border:1px solid var(--border);
            font-weight:700; font-size:13px; text-decoration:none;
            background:#f0f4fb; color:#365a99;
        }
        .btn:hover{ background:#e6eefc; }

        .btn-green{
            background:#eef7f5; color:#225c52;
        }
        .btn-green:hover{ background:#e3f3ef; }

        .input{
            width:100%;
            border:1px solid var(--border);
            border-radius:12px;
            padding:10px 12px;
            background:#fff;
            color:var(--text);
        }

        .label{ display:block; font-size:13px; font-weight:700; color:var(--text); margin-bottom:6px; }

        .grid2{ display:grid; grid-template-columns:1fr; gap:14px; margin-top:12px; }
        @media(min-width:1024px){ .grid2{ grid-template-columns:1fr 1fr; } }

        .progress{ width:100%; height:8px; background:#e5e7eb; border-radius:999px; overflow:hidden; margin-top:8px; }
        .progress div{ height:100%; }

        .badge{ font-size:12px; font-weight:700; padding:6px 10px; border-radius:12px; border:1px solid var(--border); }
        .badge.green{ background:#eef7f5; }
        .badge.orange{ background:#fff6ee; }

        .evid-grid{ display:grid; grid-template-columns:repeat(6, 1fr); gap:10px; margin-top:12px; }
        @media(max-width:1024px){ .evid-grid{ grid-template-columns:repeat(4, 1fr);} }
        @media(max-width:640px){ .evid-grid{ grid-template-columns:repeat(2, 1fr);} }

        .thumb{
            position:relative;
            border:1px solid var(--border-soft);
            border-radius:14px;
            background:#fafafa;
            overflow:hidden;
            height:92px;
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
            padding:8px;
            transition: all .15s ease;
        }
        .thumb:hover{ transform: translateY(-1px); border-color: var(--border); background:#f6f8fb; }
        .thumb img{ width:100%; height:100%; object-fit:cover; display:block; }

        .filebox{
            display:flex; flex-direction:column; gap:6px; align-items:center; justify-content:center;
        }
        .fileicon{
            width:34px; height:34px; border-radius:12px; border:1px solid var(--border);
            display:flex; align-items:center; justify-content:center;
            background:#fff; font-weight:900;
        }
        .fname{ font-size:11px; color:var(--muted); line-height:1.2; word-break:break-word; }

        .remove{
            position:absolute;
            top:6px; right:6px;
            background:#fff;
            border:1px solid var(--border);
            border-radius:999px;
            font-size:11px;
            padding:2px 6px;
            cursor:pointer;
        }
    </style>

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
