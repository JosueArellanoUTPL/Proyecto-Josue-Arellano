<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Seguimiento de Proyecto
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
        .badge{ font-size:12px; font-weight:700; padding:6px 10px; border-radius:12px; border:1px solid var(--border); }
        .badge.green{ background:#eef7f5; }
        .badge.orange{ background:#fff6ee; }
        .progress{ width:100%; height:8px; background:#e5e7eb; border-radius:999px; overflow:hidden; margin-top:8px; }
        .progress div{ height:100%; }

        .grid{ display:grid; grid-template-columns:1fr; gap:14px; margin-top:14px; }
        .evid-grid{ display:grid; grid-template-columns:repeat(6, 1fr); gap:10px; margin-top:10px; }
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
            padding:8px;
        }
        .thumb img{ width:100%; height:100%; object-fit:cover; }
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                <div class="row">
                    <div>
                        <div class="title">{{ $proyecto->nombre }}</div>
                        <div class="muted" style="margin-top:6px;">
                            Entidad: <strong>{{ $proyecto->entidad->nombre ?? '—' }}</strong> ·
                            Programa: <strong>{{ $proyecto->programa->nombre ?? '—' }}</strong>
                        </div>
                        <div class="muted" style="margin-top:6px;">
                            {{ $proyecto->descripcion ?? 'Sin descripción.' }}
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a class="btn" href="{{ route('seguimiento.programa.show', $proyecto->programa_id) }}">← Volver</a>
                        <a class="btn" href="{{ route('proyectos.avance.create', $proyecto->id) }}" style="background:#eef7f5;">
                            + Registrar avance
                        </a>
                    </div>
                </div>

                @php $p = max(0, min(100, (int)$progresoProyecto)); @endphp

                <div class="card" style="margin-top:16px;">
                    <div class="row" style="align-items:center;">
                        <div class="title">Avance actual</div>
                        <span class="badge {{ $p >= 100 ? 'green' : 'orange' }}">{{ $p }}%</span>
                    </div>
                    <div class="progress">
                        <div style="width:{{ $p }}%; background:{{ $p >= 100 ? 'var(--green)' : 'var(--orange)' }}"></div>
                    </div>
                </div>

                <div class="card" style="margin-top:14px;">
                    <div class="title">Historial de avances</div>

                    <div class="grid">
                        @foreach($proyecto->avances as $a)
                            <div class="card" style="background:#fafafa;">
                                <div class="row">
                                    <div>
                                        <strong>{{ $a->porcentaje_avance }}%</strong>
                                        <span class="muted">· {{ $a->fecha->format('d/m/Y') }}</span>
                                    </div>

                                    @if(auth()->user()->role === 'admin' || auth()->id() === $a->user_id)
                                        <div style="display:flex; gap:10px;">
                                            <a class="btn" href="{{ route('proyectos.avance.edit', $a->id) }}">Editar</a>
                                            <form method="POST" action="{{ route('proyectos.avance.destroy', $a->id) }}">
                                                @csrf @method('DELETE')
                                                <button class="btn" onclick="return confirm('¿Eliminar avance?')">Eliminar</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                {{-- Evidencias --}}
                                <div class="evid-grid">
                                    @foreach($a->evidencias as $ev)
                                        <div class="thumb">
                                            <img src="{{ asset('storage/'.$ev->path) }}">
                                            <form method="POST" action="{{ route('proyectos.avance.evidencia.delete', $ev->id) }}">
                                                @csrf @method('DELETE')
                                                <button class="remove">✕</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Agregar evidencia --}}
                                <form method="POST" enctype="multipart/form-data"
                                      action="{{ route('proyectos.avance.evidencia.add', $a->id) }}"
                                      style="margin-top:10px;">
                                    @csrf
                                    <input type="file" name="evidencia" required>
                                    <button class="btn" style="margin-left:8px;">+ Evidencia</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
