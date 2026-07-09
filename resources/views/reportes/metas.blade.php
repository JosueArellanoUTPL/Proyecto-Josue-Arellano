<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reporte de Metas
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap report-page">
                @include('reportes.partials.header', [
                    'title' => 'Reporte de Metas',
                    'subtitle' => 'Consulta de avance de metas por entidad, plan e indicadores.'
                ])

                <div class="card" style="margin-top:16px;">
                    <div class="title">Metas encontradas: {{ $metas->count() }}</div>

                    <div class="overflow-x-auto" style="margin-top:12px;">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Meta</th>
                                    <th>Entidad</th>
                                    <th>Plan</th>
                                    <th>Indicadores</th>
                                    <th>Avance</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($metas as $meta)
                                    <tr>
                                        <td>{{ $meta->codigo }}</td>
                                        <td>{{ $meta->nombre }}</td>
                                        <td>{{ $meta->plan->entidad->nombre ?? '-' }}</td>
                                        <td>{{ $meta->plan->codigo ?? '-' }}</td>
                                        <td>{{ $meta->indicadores->count() }}</td>
                                        <td>{{ $meta->indicadores->isEmpty() ? '-' : round($meta->progreso).'%' }}</td>
                                        <td>{{ $meta->estado_seguimiento }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="muted">No hay metas registradas.</td>
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
