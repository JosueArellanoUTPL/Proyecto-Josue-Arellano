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

                {{-- Filtro por entidad para revisar proyectos de una institucion especifica. --}}
                <form method="GET" action="{{ route('reportes.proyectos') }}" class="card no-print" style="margin-top:16px;">
                    <div>
                        <label class="label">Entidad</label>
                        <select class="input" name="entidad_id">
                            <option value="">Todas</option>
                            @foreach($entidades as $entidad)
                                <option value="{{ $entidad->id }}" @selected((string)request('entidad_id') === (string)$entidad->id)>
                                    {{ $entidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
                        <button class="btn" type="submit">Filtrar</button>
                        <a class="btn" href="{{ route('reportes.proyectos') }}">Limpiar</a>
                    </div>
                </form>

                {{-- Tabla del reporte: resume avance y evidencias por proyecto. --}}
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
                                    <th>Ultima fecha</th>
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
                                        <td>{{ round($proyecto->progreso, 2) }}%</td>
                                        <td>{{ $proyecto->ultimoAvance?->fecha?->format('d/m/Y') ?? '-' }}</td>
                                        <td>{{ $proyecto->avances->count() }}</td>
                                        <td>{{ $evidencias }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="muted">No hay proyectos para los filtros seleccionados.</td>
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
