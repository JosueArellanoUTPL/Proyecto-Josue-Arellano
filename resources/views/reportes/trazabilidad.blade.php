<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reporte de Trazabilidad
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap report-page">
                @include('reportes.partials.header', [
                    'title' => 'Reporte de Trazabilidad',
                    'subtitle' => 'Relacion entre metas e instrumentos estrategicos.'
                ])

                <div class="card" style="margin-top:16px;">
                    <div class="title">Registros encontrados: {{ $alineaciones->count() }}</div>

                    <div class="overflow-x-auto" style="margin-top:12px;">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Meta</th>
                                    <th>Entidad</th>
                                    <th>ODS</th>
                                    <th>PND</th>
                                    <th>Objetivo Estrategico</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alineaciones as $alineacion)
                                    <tr>
                                        <td>{{ $alineacion->meta->codigo ?? '-' }} - {{ $alineacion->meta->nombre ?? '-' }}</td>
                                        <td>{{ $alineacion->meta->plan->entidad->nombre ?? '-' }}</td>
                                        <td>{{ $alineacion->ods->codigo ?? '-' }} {{ $alineacion->ods->nombre ?? '' }}</td>
                                        <td>{{ $alineacion->pdn->codigo ?? '-' }} {{ $alineacion->pdn->nombre ?? '' }}</td>
                                        <td>{{ $alineacion->objetivoEstrategico->nombre ?? '-' }}</td>
                                        <td>{{ $alineacion->activo ? 'Activa' : 'Inactiva' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="muted">No hay registros de trazabilidad.</td>
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
