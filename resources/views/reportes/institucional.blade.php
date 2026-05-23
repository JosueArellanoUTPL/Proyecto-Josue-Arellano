<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reporte Institucional
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap report-page">
                @include('reportes.partials.header', [
                    'title' => 'Reporte Institucional',
                    'subtitle' => 'Resumen general del estado institucional registrado en el sistema.'
                ])

                <div class="kpis">
                    <div class="kpi"><div class="label">Entidades</div><div class="value">{{ $kpis['entidades'] }}</div></div>
                    <div class="kpi"><div class="label">Programas</div><div class="value">{{ $kpis['programas'] }}</div></div>
                    <div class="kpi"><div class="label">Proyectos</div><div class="value">{{ $kpis['proyectos'] }}</div></div>
                    <div class="kpi"><div class="label">Planes</div><div class="value">{{ $kpis['planes'] }}</div></div>
                    <div class="kpi"><div class="label">Metas</div><div class="value">{{ $kpis['metas'] }}</div></div>
                    <div class="kpi"><div class="label">Indicadores</div><div class="value">{{ $kpis['indicadores'] }}</div></div>
                    <div class="kpi"><div class="label">Alineaciones</div><div class="value">{{ $kpis['alineaciones'] }}</div></div>
                    <div class="kpi" style="background:#eef7f5"><div class="label">Avance metas</div><div class="value">{{ $kpis['progreso_metas'] }}%</div></div>
                    <div class="kpi" style="background:#f0f4fb"><div class="label">Avance proyectos</div><div class="value">{{ $kpis['progreso_proyectos'] }}%</div></div>
                </div>

                <div class="card" style="margin-top:16px;">
                    <div class="title">Resumen por entidad</div>

                    <div class="overflow-x-auto" style="margin-top:12px;">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Entidad</th>
                                    <th>Planes</th>
                                    <th>Metas</th>
                                    <th>Programas</th>
                                    <th>Proyectos</th>
                                    <th>Avance metas</th>
                                    <th>Avance proyectos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entidades as $entidad)
                                    @php
                                        $metas = $entidad->plans->flatMap->metas;
                                        $proyectos = $entidad->proyectos;
                                        $avanceMetas = $metas->count() ? round($metas->avg(fn($meta) => $meta->progreso), 2) : 0;
                                        $avanceProyectos = $proyectos->count() ? round($proyectos->avg(fn($proyecto) => $proyecto->progreso), 2) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $entidad->nombre }}</td>
                                        <td>{{ $entidad->plans->count() }}</td>
                                        <td>{{ $metas->count() }}</td>
                                        <td>{{ $entidad->programas->count() }}</td>
                                        <td>{{ $proyectos->count() }}</td>
                                        <td>{{ $avanceMetas }}%</td>
                                        <td>{{ $avanceProyectos }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="muted">No hay entidades registradas.</td>
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
