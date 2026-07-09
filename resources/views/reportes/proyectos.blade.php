<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reporte de Proyectos
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap report-page">
                @include('reportes.partials.header', [
                    'title' => 'Reporte de Proyectos',
                    'subtitle' => 'Consulta de avance actual, historial y evidencias por proyecto.'
                ])

                <div class="card" style="margin-top:16px;">
                    <div class="title">Proyectos encontrados: {{ $proyectos->count() }}</div>

                    <div class="overflow-x-auto" style="margin-top:12px;">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Proyecto</th>
                                    <th>Entidad</th>
                                    <th>Programa</th>
                                    <th>Avance</th>
                                    <th>Última fecha</th>
                                    <th>Registros</th>
                                    <th>Evidencias</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proyectos as $proyecto)
                                    @php
                                        // Cuenta todas las evidencias de los avances del proyecto.
                                        $evidencias = $proyecto->avances->sum(fn($avance) => $avance->evidencias->count());
                                    @endphp
                                    <tr>
                                        <td>{{ $proyecto->nombre }}</td>
                                        <td>{{ $proyecto->entidad->nombre ?? '-' }}</td>
                                        <td>{{ $proyecto->programa->nombre ?? '-' }}</td>
                                        <td>{{ round($proyecto->progreso) }}%</td>
                                        <td>{{ $proyecto->ultimoAvance?->fecha?->format('d/m/Y') ?? '-' }}</td>
                                        <td>{{ $proyecto->avances->count() }}</td>
                                        <td>{{ $evidencias }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="muted">No hay proyectos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
