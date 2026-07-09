@php
    $alineacionActual = $alineacion ?? null;
@endphp

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
                    {{ old('meta_id', $alineacionActual?->meta_id) == $meta->id ? 'selected' : '' }}>
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
                        <option value="{{ $objetivoOds->id }}" {{ old('ods_id', $alineacionActual?->ods_id) == $objetivoOds->id ? 'selected' : '' }}>
                            {{ $objetivoOds->codigo }} - {{ $objetivoOds->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label">Objetivo Estratégico</label>
                <select name="objetivo_estrategico_id" class="input">
                    <option value="">(Ninguno)</option>
                    @foreach ($objetivos as $objetivo)
                        <option value="{{ $objetivo->id }}" {{ old('objetivo_estrategico_id', $alineacionActual?->objetivo_estrategico_id) == $objetivo->id ? 'selected' : '' }}>
                            {{ $objetivo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div>
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="activo" value="1" {{ old('activo', $alineacionActual?->activo ?? true) ? 'checked' : '' }}>
            <span style="font-weight:700; color:var(--text);">Activo</span>
        </label>
        <div class="muted" style="margin-top:6px;">
            Al desactivar una alineación, se conserva para trazabilidad pero deja de considerarse vigente.
        </div>
    </div>
</div>

{{-- Filtro de metas por entidad. --}}
<script>
    (function () {
        const entidadSelect = document.getElementById('entidad_filtro');
        const metaSelect = document.getElementById('meta_id');

        if (!entidadSelect || !metaSelect) return;

        const metaOptions = Array.from(metaSelect.querySelectorAll('option'));

        function aplicarFiltro(limpiarMeta = false) {
            const entidadId = entidadSelect.value;

            metaOptions.forEach(option => {
                if (!option.value) return;
                option.hidden = entidadId ? option.dataset.entidad !== entidadId : true;
            });

            if (limpiarMeta) metaSelect.value = '';
        }

        const metaSeleccionada = metaOptions.find(option => option.selected && option.value);
        if (metaSeleccionada) entidadSelect.value = metaSeleccionada.dataset.entidad;

        entidadSelect.addEventListener('change', () => aplicarFiltro(true));
        aplicarFiltro();
    })();
</script>
