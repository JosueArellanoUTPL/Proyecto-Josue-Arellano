<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Alineación
        </h2>
    </x-slot>
    {{-- Los estilos reutilizables de esta vista ahora estan en resources/css/app.css --}}

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                <div class="title">Editar Alineación</div>
                <div class="muted" style="margin-top:6px;">
                    Se actualiza la meta/indicador y los instrumentos estratégicos asociados.
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

                <form method="POST" action="{{ route('alineaciones.update', $alineacion->id) }}" class="card" style="margin-top:16px;">
                    @csrf
                    @method('PUT')

                    <div style="display:grid; gap:12px;">
                        <div>
                            <label class="label">Meta (obligatorio)</label>
                            <select id="meta_id" name="meta_id" class="input">
                                @foreach ($metas as $m)
                                    <option value="{{ $m->id }}" {{ old('meta_id', $alineacion->meta_id) == $m->id ? 'selected' : '' }}>
                                        {{ $m->codigo }} - {{ $m->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="label">Indicador (opcional)</label>
                            <div class="muted" style="margin-bottom:6px;">
                                Se muestran indicadores que pertenecen a la meta seleccionada.
                            </div>
                            <select id="indicador_id" name="indicador_id" class="input">
                                <option value="">(Ninguno)</option>
                                @foreach ($indicadores as $i)
                                    <option value="{{ $i->id }}"
                                            data-meta="{{ $i->meta_id }}"
                                            {{ old('indicador_id', $alineacion->indicador_id) == $i->id ? 'selected' : '' }}>
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
                                            <option value="{{ $o->id }}" {{ old('ods_id', $alineacion->ods_id) == $o->id ? 'selected' : '' }}>
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
                                            <option value="{{ $p->id }}" {{ old('pdn_id', $alineacion->pdn_id) == $p->id ? 'selected' : '' }}>
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
                                            <option value="{{ $obj->id }}" {{ old('objetivo_estrategico_id', $alineacion->objetivo_estrategico_id) == $obj->id ? 'selected' : '' }}>
                                                {{ $obj->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="activo" value="1" {{ old('activo', $alineacion->activo) ? 'checked' : '' }}>
                                <span style="font-weight:700; color:var(--text);">Activo</span>
                            </label>
                        </div>
                    </div>

                    <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-green">Actualizar</button>
                        <a href="{{ route('alineaciones.index') }}" class="btn">Volver</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        (function () {
            const metaSelect = document.getElementById('meta_id');
            const indSelect  = document.getElementById('indicador_id');

            if (!metaSelect || !indSelect) return;

            const allOptions = Array.from(indSelect.querySelectorAll('option'));

            function applyFilter() {
                const metaId = metaSelect.value;
                const current = indSelect.value;

                allOptions.forEach(opt => {
                    if (!opt.value) return;
                    const belongs = opt.getAttribute('data-meta');
                    opt.hidden = metaId ? (belongs !== metaId) : false;
                });

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
