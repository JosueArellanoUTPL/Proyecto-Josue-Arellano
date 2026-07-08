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

                {{-- Filtros. --}}
                <form method="GET" action="{{ route('reportes.metas') }}" class="card no-print" style="margin-top:16px;">
                    <div class="grid2">
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

                        <div>
                            <label class="label">Estado</label>
                            <select class="input" name="estado">
                                <option value="">Todos</option>
                                <option value="completadas" @selected(request('estado') === 'completadas')>Completadas</option>
                                <option value="en_progreso" @selected(request('estado') === 'en_progreso')>En progreso</option>
                                <option value="pendientes" @selected(request('estado') === 'pendientes')>Pendientes de indicadores</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
                        <button class="btn" type="submit">Filtrar</button>
                        <a class="btn" href="{{ route('reportes.metas') }}">Limpiar</a>
                    </div>
                </form>

                {{-- Resultados. --}}
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
                                        <td colspan="7" class="muted">No hay metas para los filtros seleccionados.</td>
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
