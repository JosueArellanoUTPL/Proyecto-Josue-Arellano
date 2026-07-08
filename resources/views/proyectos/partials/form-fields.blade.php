@php
    // Valores del formulario.
    $proyectoActual = $proyecto ?? null;
    $entidadSeleccionada = old('entidad_filtro', $proyectoActual?->programa?->entidad_id);
    $programaSeleccionado = old('programa_id', $proyectoActual?->programa_id);
@endphp

{{-- Campos del proyecto. --}}
<div data-project-fields>
    <div class="mb-4">
        <label class="block mb-1">CÃ³digo</label>
        <input name="codigo" value="{{ old('codigo', $proyectoActual?->codigo) }}"
               class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block mb-1">Nombre</label>
        <input name="nombre" value="{{ old('nombre', $proyectoActual?->nombre) }}"
               class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block mb-1">DescripciÃ³n</label>
        <textarea name="descripcion" class="w-full border rounded px-3 py-2"
                  rows="3">{{ old('descripcion', $proyectoActual?->descripcion) }}</textarea>
    </div>

    <div class="mb-4">
        <label class="block mb-1">Entidad (filtro)</label>
        <select name="entidad_filtro" class="w-full border rounded px-3 py-2" data-entity-select required>
            <option value="">Seleccione una entidad</option>
            @foreach ($entidades as $entidad)
                <option value="{{ $entidad->id }}" @selected((string) $entidadSeleccionada === (string) $entidad->id)>
                    {{ $entidad->nombre }}
                </option>
            @endforeach
        </select>
        <p class="text-sm text-gray-500 mt-1">
            La entidad se obtiene automaticamente desde el programa seleccionado.
        </p>
    </div>

    <div class="mb-4">
        <label class="block mb-1">Programa</label>
        <select name="programa_id" class="w-full border rounded px-3 py-2" data-related-select required>
            <option value="">Seleccione un programa</option>
            @foreach ($programas as $programa)
                <option value="{{ $programa->id }}" data-entity="{{ $programa->entidad_id }}"
                        @selected((string) $programaSeleccionado === (string) $programa->id)>
                    {{ $programa->nombre }}
                </option>
            @endforeach
        </select>
    </div>


    <div class="mb-4">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="activo" {{ old('activo', $proyectoActual?->activo ?? true) ? 'checked' : '' }}>
            <span>Activo</span>
        </label>
    </div>
</div>

{{-- Filtro de programas por entidad. --}}
<script>
    // Filtro por entidad.
    document.addEventListener('DOMContentLoaded', function () {
        const fields = document.querySelector('[data-project-fields]');
        const entitySelect = fields?.querySelector('[data-entity-select]');
        const relatedSelects = fields?.querySelectorAll('[data-related-select]') ?? [];

        function filterRelatedOptions() {
            const entityId = entitySelect.value;

            relatedSelects.forEach(function (select) {
                Array.from(select.options).forEach(function (option) {
                    const allowed = !option.value || (entityId && option.dataset.entity === entityId);
                    option.hidden = !allowed;
                    option.disabled = !allowed;
                });

                if (select.selectedOptions[0]?.disabled) {
                    select.value = '';
                }
            });
        }

        entitySelect?.addEventListener('change', filterRelatedOptions);
        filterRelatedOptions();
    });
</script>
