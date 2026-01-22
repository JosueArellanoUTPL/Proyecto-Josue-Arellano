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
            background:#fff;
            font-weight:900;
        }
        .fname{ font-size:11px; color:var(--muted); line-height:1.2; word-break:break-word; }
    </style>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                @if(session('success'))
                    <div class="card" style="border-color:#bfe3da; background:#f3fbf9; margin-bottom:14px;">
                        <div class="title">Listo</div>
                        <div class="muted" style="margin-top:6px;">{{ session('success') }}</div>
                    </div>
                @endif

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
                        <a class="btn" href="{{ route('proyectos.avance.create', $proyecto->id) }}" style="background:#eef7f5; color:#225c52;">
                            + Registrar avance
                        </a>
                    </div>
                </div>

                @php $p = max(0, min(100, (int)$progresoProyecto)); @endphp

                <div class="card" style="margin-top:16px;">
                    <div class="row" style="align-items:center;">
                        <div>
                            <div class="title">Avance actual</div>
                            <div class="muted" style="margin-top:6px;">
                                Basado en el último avance registrado.
                            </div>
                        </div>
                        <span class="badge {{ $p >= 100 ? 'green' : 'orange' }}">{{ $p }}%</span>
                    </div>

                    <div class="progress">
                        <div style="width:{{ $p }}%; background:{{ $p >= 100 ? 'var(--green)' : 'var(--orange)' }}"></div>
                    </div>
                </div>

                <div class="card" style="margin-top:14px;">
                    <div class="title">Historial de avances</div>
                    <div class="muted" style="margin-top:6px;">Cada avance puede tener varias evidencias en miniatura.</div>

                    <div class="grid">
                        @forelse($proyecto->avances as $a)
                            @php
                                $ap = max(0, min(100, (int)round($a->porcentaje_avance)));
                            @endphp

                            <div class="card" style="border-color:var(--border-soft); background:#fafafa;">
                                <div class="row" style="align-items:center;">
                                    <div>
                                        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                            <span class="badge {{ $ap >= 100 ? 'green' : 'orange' }}">{{ $ap }}%</span>
                                            <span class="muted">
                                                {{ $a->fecha?->format('d/m/Y') ?? '' }}
                                                · {{ $a->user->name ?? 'Usuario' }}
                                            </span>
                                        </div>
                                        @if($a->comentario)
                                            <div class="muted" style="margin-top:8px;">{{ $a->comentario }}</div>
                                        @endif
                                    </div>
                                </div>

                                @if($a->evidencias->count())
                                    <div class="evid-grid">
                                        @foreach($a->evidencias as $ev)
                                            @php
                                                $url = asset('storage/' . $ev->path);
                                                $isImg = $ev->mime_type && str_starts_with($ev->mime_type, 'image/');
                                                $label = $ev->original_name ?? 'Archivo';
                                            @endphp

                                            <a href="{{ $url }}" target="_blank" style="text-decoration:none;">
                                                <div class="thumb">
                                                    @if($isImg)
                                                        <img src="{{ $url }}" alt="{{ $label }}">
                                                    @else
                                                        <div class="filebox">
                                                            <div class="fileicon">📄</div>
                                                            <div class="fname">{{ \Illuminate\Support\Str::limit($label, 22) }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="muted" style="margin-top:10px;">Sin evidencias en este avance.</div>
                                @endif
                            </div>
                        @empty
                            <div class="muted" style="margin-top:10px;">Aún no hay avances registrados.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

