<x-app-layout>
    {{-- Seccion de encabezado. --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Alineación
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                <div class="title">Configuración de Alineación</div>
                <div class="muted" style="margin-top:6px;">
                    Se registra una relación entre una meta y los instrumentos estratégicos.
                </div>

                <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
                    <span class="chip"><span class="dot blue"></span>Instrumentos</span>
                    <span class="chip"><span class="dot green"></span>Vinculación</span>
                    <span class="chip"><span class="dot orange"></span>Validación</span>
                </div>

                {{-- Mensajes de validacion. --}}
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
                    {{-- Formulario de datos. --}}
                    @csrf

                    <div style="display:grid; gap:12px;">
                        <div>
                            <label class="label">Entidad</label>
                            <div class="muted" style="margin-bottom:6px;">
                                Primero selecciona una entidad para ver solamente sus metas.
                            </div>
                            <select id="entidad_filtro" class="input">
                                <option value="">Seleccione</option>
                                @foreach ($entidades as $entidad)
                                    <option value="{{ $entidad->id }}">
                                        {{ $entidad->codigo }} - {{ $entidad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="label">Meta (obligatorio)</label>
                            <select id="meta_id" name="meta_id" class="input">
                                <option value="">Seleccione</option>
                                @foreach ($metas as $meta)
                                    <option value="{{ $meta->id }}"
                                        data-entidad="{{ $meta->plan?->entidad_id }}"
                                        {{ old('meta_id') == $meta->id ? 'selected' : '' }}>
                                        {{ $meta->codigo }} - {{ $meta->nombre }} (PND: {{ $meta->plan?->pdn?->codigo ?? 'Sin asignar' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="hint">
                            <div class="title">Instrumentos estratégicos</div>
                            <div class="muted" style="margin-top:6px;">
                                El PND se toma automáticamente del plan. Selecciona al menos un ODS o un objetivo estratégico.
                            </div>

                            <div style="display:grid; gap:12px; margin-top:12px;">
                                <div>
                                    <label class="label">ODS</label>
                                    <select name="ods_id" class="input">
                                        <option value="">(Ninguno)</option>
                                        @foreach ($ods as $objetivoOds)
                                            <option value="{{ $objetivoOds->id }}" {{ old('ods_id') == $objetivoOds->id ? 'selected' : '' }}>
                                                {{ $objetivoOds->codigo }} - {{ $objetivoOds->nombre }}
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
        // Filtra las metas para mostrar solo las de la entidad seleccionada.
        (function () {
            const entidadSelect = document.getElementById('entidad_filtro');
            const metaSelect = document.getElementById('meta_id');

            if (!entidadSelect || !metaSelect) return;

            const metaOptions = Array.from(metaSelect.querySelectorAll('option'));

            function applyMetaFilter(resetMeta = false) {
                const entidadId = entidadSelect.value;

                metaOptions.forEach(option => {
                    if (!option.value) return;
                    option.hidden = entidadId ? option.dataset.entidad !== entidadId : true;
                });

                if (resetMeta) {
                    metaSelect.value = '';
                }

            }

            const selectedMeta = metaOptions.find(option => option.selected && option.value);
            if (selectedMeta) {
                entidadSelect.value = selectedMeta.dataset.entidad;
            }

            entidadSelect.addEventListener('change', () => applyMetaFilter(true));
            applyMetaFilter();
        })();
    </script>
</x-app-layout>
