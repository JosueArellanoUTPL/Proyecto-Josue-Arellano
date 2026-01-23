<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Alineación
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
        .label{ font-weight:700; font-size:13px; color:var(--text); margin-bottom:6px; display:block; }
        .input{ width:100%; border:1px solid var(--border); border-radius:12px; padding:10px 12px; background:#fff; }
        .btn{
            display:inline-flex; align-items:center; justify-content:center;
            height:34px; padding:0 14px; border-radius:10px;
            border:1px solid var(--border); font-weight:700; font-size:13px;
            text-decoration:none; background:#f0f4fb; color:#365a99;
        }
        .btn:hover{ background:#e6eefc; }
        .btn-green{ background:#eef7f5; color:#225c52; }
        .btn-green:hover{ background:#e3f3ef; }
        .chip{
            display:inline-flex; align-items:center; gap:8px;
            font-size:12px; font-weight:700;
            padding:8px 12px; border-radius:999px;
            border:1px solid var(--border);
            background:#fff;
            color:var(--text);
        }
        .dot{ width:10px; height:10px; border-radius:999px; border:1px solid rgba(0,0,0,.08); }
        .dot.blue{ background:var(--blue); }
        .dot.green{ background:var(--green); }
        .dot.orange{ background:var(--orange); }
        .errbox{ margin-top:16px; padding:12px; border-radius:14px; border:1px solid #f3b4b4; background:#fff5f5; }
        .hint{ margin-top:10px; padding:12px; border-radius:14px; border:1px solid var(--border-soft); background:#fafafa; }
    </style>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                <div class="title">Configuración de Alineación</div>
                <div class="muted" style="margin-top:6px;">
                    Se registra una relación entre una meta (y opcionalmente un indicador) con instrumentos estratégicos.
                </div>

                <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
                    <span class="chip"><span class="dot blue"></span>Instrumentos</span>
                    <span class="chip"><span class="dot green"></span>Vinculación</span>
                    <span class="chip"><span class="dot orange"></span>Validación</span>
                </div>

                @if ($errors->any())
                    <div class="errbox">
                        <div class="title">Revisar campos</div>
                        <ul class="muted" style="margin-top:8px; list-style:disc; padding-left:18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('alineaciones.store') }}" class="card" style="margin-top:16px;">
                    @csrf

                    <div style="display:grid; gap:12px;">
                        <div>
                            <label class="label">Meta (obligatorio)</label>
                            <select id="meta_id" name="meta_id" class="input">
                                <option value="">Seleccione</option>
                                @foreach ($metas as $m)
                                    <option value="{{ $m->id }}" {{ old('meta_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->codigo }} - {{ $m->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="label">Indicador (opcional)</label>
                            <div class="muted" style="margin-bottom:6px;">
                                Solo se muestran indicadores que pertenecen a la meta seleccionada.
                            </div>
                            <select id="indicador_id" name="indicador_id" class="input">
                                <option value="">(Ninguno)</option>
                                @foreach ($indicadores as $i)
                                    <option value="{{ $i->id }}"
                                            data-meta="{{ $i->meta_id }}"
                                            {{ old('indicador_id') == $i->id ? 'selected' : '' }}>
                                        {{ $i->codigo }} - {{ $i->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="hint">
                            <div class="title">Instrumentos estratégicos</div>
                            <div class="muted" style="margin-top:6px;">
                                Se requiere seleccionar al menos uno: ODS, PDN o Objetivo Estratégico.
                            </div>

                            <div style="display:grid; gap:12px; margin-top:12px;">
                                <div>
                                    <label class="label">ODS</label>
                                    <select name="ods_id" class="input">
                                        <option value="">(Ninguno)</option>
                                        @foreach ($ods as $o)
                                            <option value="{{ $o->id }}" {{ old('ods_id') == $o->id ? 'selected' : '' }}>
                                                {{ $o->codigo }} - {{ $o->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="label">PDN</label>
                                    <select name="pdn_id" class="input">
                                        <option value="">(Ninguno)</option>
                                        @foreach ($pdns as $p)
                                            <option value="{{ $p->id }}" {{ old('pdn_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->codigo }} - {{ $p->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="label">Objetivo Estratégico</label>
                                    <select name="objetivo_estrategico_id" class="input">
                                        <option value="">(Ninguno)</option>
                                        @foreach ($objetivos as $obj)
                                            <option value="{{ $obj->id }}" {{ old('objetivo_estrategico_id') == $obj->id ? 'selected' : '' }}>
                                                {{ $obj->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}>
                                <span style="font-weight:700; color:var(--text);">Activo</span>
                            </label>
                            <div class="muted" style="margin-top:6px;">
                                Al desactivar una alineación, se mantiene el registro para trazabilidad pero no se considera vigente.
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-green">Guardar</button>
                        <a href="{{ route('alineaciones.index') }}" class="btn">Cancelar</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        // Filtrado de indicadores según la meta seleccionada.
        // Esto evita seleccionar un indicador que no corresponde a la meta.
        (function () {
            const metaSelect = document.getElementById('meta_id');
            const indSelect  = document.getElementById('indicador_id');

            if (!metaSelect || !indSelect) return;

            const allOptions = Array.from(indSelect.querySelectorAll('option'));

            function applyFilter() {
                const metaId = metaSelect.value;
                const current = indSelect.value;

                allOptions.forEach(opt => {
                    if (!opt.value) return; // "(Ninguno)" siempre visible
                    const belongs = opt.getAttribute('data-meta');
                    opt.hidden = metaId ? (belongs !== metaId) : false;
                });

                // Si el indicador seleccionado no corresponde a la meta, se resetea a "(Ninguno)"
                const selected = allOptions.find(o => o.value === current);
                if (selected && selected.hidden) {
                    indSelect.value = '';
                }
            }

            metaSelect.addEventListener('change', applyFilter);
            applyFilter();
        })();
    </script>
</x-app-layout>
