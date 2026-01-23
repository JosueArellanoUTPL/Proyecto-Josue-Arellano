<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Alineaciones
        </h2>
    </x-slot>

    <style>
        :root{
            --beige:#f5f1ea; --card:#ffffff;
            --border:#cfd5dd; --border-soft:#d9dee6;
            --blue:#8faadc; --green:#9fd3c7; --orange:#f4c095;
            --text:#1f2937; --muted:#6b7280;
        }
        .wrap{ background:var(--beige); border-radius:20px; padding:28px; border:1px solid var(--border-soft); }
        .card{ background:var(--card); border-radius:18px; padding:18px; border:1px solid var(--border); }
        .title{ font-weight:800; font-size:18px; color:var(--text); }
        .muted{ color:var(--muted); font-size:13px; }
        .btn{
            display:inline-flex; align-items:center; justify-content:center;
            height:34px; padding:0 14px; border-radius:10px;
            border:1px solid var(--border); font-weight:700; font-size:13px;
            text-decoration:none; background:#f0f4fb; color:#365a99;
        }
        .btn:hover{ background:#e6eefc; }

        .grid{ display:grid; grid-template-columns:1fr; gap:14px; margin-top:16px; }
        @media(min-width:900px){ .grid{ grid-template-columns:1fr 1fr; } }

        .row{ display:flex; justify-content:space-between; gap:10px; align-items:flex-start; }
        .pill{
            display:inline-flex; align-items:center; gap:8px;
            font-size:12px; font-weight:800;
            padding:6px 10px; border-radius:999px;
            border:1px solid var(--border);
            background:#fff;
        }
        .dot{ width:10px; height:10px; border-radius:999px; border:1px solid rgba(0,0,0,.08); }
        .dot.blue{ background:var(--blue); }
        .dot.green{ background:var(--green); }
        .dot.orange{ background:var(--orange); }

        .actions{ display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; }
        .btn-mini{
            height:30px; padding:0 12px; border-radius:10px;
            border:1px solid var(--border);
            font-weight:800; font-size:12px;
            background:#fafafa; color:var(--text);
            text-decoration:none;
        }
        .btn-mini:hover{ background:#f6f8fb; }
        .btn-warn{ background:#fff6ee; }
        .btn-danger{ background:#fff0f0; border-color:#f2b9b9; }

        .kv{ margin-top:10px; display:grid; gap:8px; }
        .kv .line{ display:flex; justify-content:space-between; gap:10px; }
    </style>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                @if (session('success'))
                    <div class="card" style="border-color:#bfe3da; background:#f3fbf9; margin-bottom:14px;">
                        <div class="title">Listo</div>
                        <div class="muted" style="margin-top:6px;">{{ session('success') }}</div>
                    </div>
                @endif

                <div class="row">
                    <div>
                        <div class="title">Mapa de alineaciones</div>
                        <div class="muted" style="margin-top:6px;">
                            Cada registro relaciona una meta (y opcionalmente un indicador) con instrumentos estratégicos.
                        </div>
                    </div>

                    <a href="{{ route('alineaciones.create') }}" class="btn">+ Nueva Alineación</a>
                </div>

                <div class="grid">
                    @forelse ($alineaciones as $a)
                        <div class="card" style="background:#fff;">
                            <div class="row">
                                <div>
                                    <div class="title" style="font-size:16px;">
                                        {{ $a->meta->codigo ?? 'META' }} — {{ $a->meta->nombre ?? '-' }}
                                    </div>

                                    <div class="muted" style="margin-top:6px;">
                                        Indicador: <strong>{{ $a->indicador->codigo ?? '-' }}</strong> {{ $a->indicador->nombre ?? '' }}
                                    </div>

                                    <div style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;">
                                        @if($a->ods_id)
                                            <span class="pill"><span class="dot blue"></span>ODS</span>
                                        @endif
                                        @if($a->pdn_id)
                                            <span class="pill"><span class="dot green"></span>PDN</span>
                                        @endif
                                        @if($a->objetivo_estrategico_id)
                                            <span class="pill"><span class="dot orange"></span>OE</span>
                                        @endif
                                        @if(!$a->ods_id && !$a->pdn_id && !$a->objetivo_estrategico_id)
                                            <span class="pill">Sin instrumentos</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="actions">
                                    <a class="btn-mini btn-warn" href="{{ route('alineaciones.edit', $a->id) }}">Editar</a>
                                    <form method="POST"
                                          action="{{ route('alineaciones.destroy', $a->id) }}"
                                          onsubmit="return confirm('¿Eliminar esta alineación?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-mini btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </div>

                            <div class="kv">
                                <div class="line">
                                    <span class="muted">ODS</span>
                                    <span style="font-weight:800; color:var(--text);">
                                        {{ $a->ods->codigo ?? '-' }} {{ $a->ods->nombre ?? '' }}
                                    </span>
                                </div>
                                <div class="line">
                                    <span class="muted">PDN</span>
                                    <span style="font-weight:800; color:var(--text);">
                                        {{ $a->pdn->codigo ?? '-' }} {{ $a->pdn->nombre ?? '' }}
                                    </span>
                                </div>
                                <div class="line">
                                    <span class="muted">Objetivo Estratégico</span>
                                    <span style="font-weight:800; color:var(--text);">
                                        {{ $a->objetivoEstrategico->nombre ?? '-' }}
                                    </span>
                                </div>
                                <div class="line">
                                    <span class="muted">Estado</span>
                                    <span style="font-weight:800; color:var(--text);">
                                        {{ $a->activo ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card">
                            <div class="title">Sin registros</div>
                            <div class="muted" style="margin-top:6px;">
                                No hay alineaciones registradas.
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

