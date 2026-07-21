<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Revisión de Actividad Operativa</h2></x-slot>

    <div class="py-6"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8"><div class="bg-white shadow rounded p-6">
        <x-form-errors />

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div><dt class="font-semibold">Código</dt><dd>{{ $actividadOperativa->codigo }}</dd></div>
            <div><dt class="font-semibold">Plan</dt><dd>{{ $actividadOperativa->plan->nombre }}</dd></div>
            <div><dt class="font-semibold">Actividad</dt><dd>{{ $actividadOperativa->nombre }}</dd></div>
            <div><dt class="font-semibold">Responsable</dt><dd>{{ $actividadOperativa->responsable }}</dd></div>
            <div><dt class="font-semibold">Meta operativa</dt><dd>{{ $actividadOperativa->meta_operativa }}</dd></div>
            <div><dt class="font-semibold">Presupuesto</dt><dd>USD {{ number_format($actividadOperativa->presupuesto, 2) }}</dd></div>
        </dl>

        <form method="POST" action="{{ route('actividades-operativas.decision', $actividadOperativa) }}">
            @csrf
            <div class="mb-4">
                <label class="block mb-1">Decisión</label>
                <select name="decision" class="w-full border rounded px-3 py-2">
                    <option value="aprobada">Aprobar</option>
                    <option value="observada">Observar</option>
                    <option value="rechazada">Rechazar</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-1">Comentario</label>
                <textarea name="comentario_revision" rows="4" class="w-full border rounded px-3 py-2">{{ old('comentario_revision') }}</textarea>
                <p class="text-sm text-gray-500 mt-1">Obligatorio cuando la actividad se observa o rechaza.</p>
            </div>
            <div class="flex gap-3"><button class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded transition">Registrar decisión</button><a href="{{ route('actividades-operativas.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded transition">Volver</a></div>
        </form>
    </div></div></div>
</x-app-layout>
