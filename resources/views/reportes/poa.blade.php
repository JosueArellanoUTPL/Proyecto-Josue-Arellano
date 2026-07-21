<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Reporte de Actividades Operativas (POA)</h2></x-slot>
    <div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4"><a href="{{ route('reportes.poa.csv') }}" class="btn btn-green">Descargar CSV</a></div>
        <div class="bg-white shadow rounded p-4 overflow-x-auto"><table class="w-full">
            <thead><tr class="text-left border-b"><th class="py-2">Código</th><th class="py-2">Actividad</th><th class="py-2">Proyecto</th><th class="py-2">Responsable</th><th class="py-2">Avance</th><th class="py-2">Presupuesto</th><th class="py-2">Estado</th></tr></thead>
            <tbody>@forelse($actividades as $actividad)
                <tr class="border-b"><td class="py-2">{{ $actividad->codigo }}</td><td class="py-2">{{ $actividad->nombre }}</td><td class="py-2">{{ $actividad->proyecto->nombre }}</td><td class="py-2">{{ $actividad->responsable }}</td><td class="py-2">{{ $actividad->avance }}%</td><td class="py-2">USD {{ number_format($actividad->presupuesto, 2) }}</td><td class="py-2">{{ \App\Models\ActividadOperativa::ESTADOS[$actividad->estado] }}</td></tr>
            @empty<tr><td colspan="7" class="py-4 text-center text-gray-500">No hay actividades operativas activas.</td></tr>@endforelse</tbody>
        </table></div>
    </div></div>
</x-app-layout>
