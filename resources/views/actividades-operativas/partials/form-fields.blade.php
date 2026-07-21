<div class="mb-4">
    <label class="block mb-1">Código</label>
    <input name="codigo" value="{{ old('codigo', $actividadOperativa->codigo ?? '') }}" class="w-full border rounded px-3 py-2">
</div>
<div class="mb-4">
    <label class="block mb-1">Proyecto / programa asociado</label>
    <select name="proyecto_id" class="w-full border rounded px-3 py-2">
        <option value="">Seleccione</option>
        @foreach ($proyectos as $proyecto)
            <option value="{{ $proyecto->id }}" @selected(old('proyecto_id', $actividadOperativa->proyecto_id ?? '') == $proyecto->id)>{{ $proyecto->codigo }} - {{ $proyecto->nombre }} ({{ $proyecto->programa?->nombre }})</option>
        @endforeach
    </select>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4">
        <label class="block mb-1">Objetivo estratégico</label>
        <select name="objetivo_estrategico_id" class="w-full border rounded px-3 py-2">
            <option value="">Seleccione</option>
            @foreach ($objetivos as $objetivo)
                <option value="{{ $objetivo->id }}" @selected(old('objetivo_estrategico_id', $actividadOperativa->objetivo_estrategico_id ?? '') == $objetivo->id)>{{ $objetivo->codigo }} - {{ $objetivo->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block mb-1">Indicador</label>
        <select name="indicador_id" class="w-full border rounded px-3 py-2">
            <option value="">Seleccione</option>
            @foreach ($indicadores as $indicador)
                <option value="{{ $indicador->id }}" @selected(old('indicador_id', $actividadOperativa->indicador_id ?? '') == $indicador->id)>{{ $indicador->codigo }} - {{ $indicador->nombre }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mb-4">
    <label class="block mb-1">Actividad operativa</label>
    <input name="nombre" value="{{ old('nombre', $actividadOperativa->nombre ?? '') }}" class="w-full border rounded px-3 py-2">
</div>
<div class="mb-4">
    <label class="block mb-1">Descripción</label>
    <textarea name="descripcion" rows="3" class="w-full border rounded px-3 py-2">{{ old('descripcion', $actividadOperativa->descripcion ?? '') }}</textarea>
</div>
<div class="mb-4">
    <label class="block mb-1">Plan institucional</label>
    <select name="plan_id" class="w-full border rounded px-3 py-2">
        <option value="">Seleccione</option>
        @foreach ($planes as $plan)
            <option value="{{ $plan->id }}" @selected(old('plan_id', $actividadOperativa->plan_id ?? '') == $plan->id)>{{ $plan->codigo }} - {{ $plan->nombre }}</option>
        @endforeach
    </select>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4"><label class="block mb-1">Responsable</label><input name="responsable" value="{{ old('responsable', $actividadOperativa->responsable ?? '') }}" class="w-full border rounded px-3 py-2"></div>
    <div class="mb-4"><label class="block mb-1">Año</label><input type="number" name="anio" value="{{ old('anio', $actividadOperativa->anio ?? date('Y')) }}" class="w-full border rounded px-3 py-2"></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4"><label class="block mb-1">Fecha de inicio</label><input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', isset($actividadOperativa) && $actividadOperativa->fecha_inicio ? $actividadOperativa->fecha_inicio->format('Y-m-d') : '') }}" class="w-full border rounded px-3 py-2"></div>
    <div class="mb-4"><label class="block mb-1">Fecha de fin</label><input type="date" name="fecha_fin" value="{{ old('fecha_fin', isset($actividadOperativa) && $actividadOperativa->fecha_fin ? $actividadOperativa->fecha_fin->format('Y-m-d') : '') }}" class="w-full border rounded px-3 py-2"></div>
</div>
<div class="mb-4">
    <label class="block mb-1">Evidencia (PDF, JPG o PNG; máximo 2 MB)</label>
    <input type="file" name="evidencia" accept=".pdf,.jpg,.jpeg,.png" class="w-full border rounded px-3 py-2">
    @if (! empty($actividadOperativa?->evidencia))
        <a href="{{ Storage::url($actividadOperativa->evidencia) }}" target="_blank" class="text-blue-600 text-sm">Ver evidencia actual</a>
    @endif
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4"><label class="block mb-1">Meta operativa</label><input name="meta_operativa" value="{{ old('meta_operativa', $actividadOperativa->meta_operativa ?? '') }}" class="w-full border rounded px-3 py-2"></div>
    <div class="mb-4"><label class="block mb-1">Meta anual (%)</label><input type="number" min="1" max="100" name="meta_anual" value="{{ old('meta_anual', $actividadOperativa->meta_anual ?? 100) }}" class="w-full border rounded px-3 py-2"></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4"><label class="block mb-1">Unidad de medida</label><select name="unidad_medida" class="w-full border rounded px-3 py-2">@foreach(['%' => 'Porcentaje (%)', 'unidad' => 'Unidad', 'actividad' => 'Actividad'] as $valor => $etiqueta)<option value="{{ $valor }}" @selected(old('unidad_medida', $actividadOperativa->unidad_medida ?? '%') === $valor)>{{ $etiqueta }}</option>@endforeach</select></div>
    <div class="mb-4"><label class="block mb-1">Prioridad</label><select name="prioridad" class="w-full border rounded px-3 py-2">@foreach(['alta' => 'Alta', 'media' => 'Media', 'baja' => 'Baja'] as $valor => $etiqueta)<option value="{{ $valor }}" @selected(old('prioridad', $actividadOperativa->prioridad ?? 'media') === $valor)>{{ $etiqueta }}</option>@endforeach</select></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4"><label class="block mb-1">Avance físico (%)</label><input type="number" min="0" max="100" name="avance" value="{{ old('avance', $actividadOperativa->avance ?? 0) }}" class="w-full border rounded px-3 py-2"></div>
    <div class="mb-4"><label class="block mb-1">Presupuesto (USD)</label><input type="number" min="0" step="0.01" name="presupuesto" value="{{ old('presupuesto', $actividadOperativa->presupuesto ?? 0) }}" class="w-full border rounded px-3 py-2"></div>
</div>
<div class="mb-4"><label class="inline-flex items-center gap-2"><input type="checkbox" name="activo" value="1" @checked(old('activo', $actividadOperativa->activo ?? true))><span>Activa</span></label></div>
