<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Actividades Operativas (POA)</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-success-message />

            @if(auth()->user()->canManagePlanning())
            <div class="mb-4">
                <a href="{{ route('actividades-operativas.create') }}" class="btn btn-blue">
                    + Nueva actividad
                </a>
            </div>
            @endif

            <div class="bg-white shadow rounded p-4 overflow-x-auto">
                <table class="w-full poa-table">
                    <colgroup>
                        <col style="width: 120px">
                        <col style="width: 280px">
                        <col style="width: 190px">
                        <col style="width: 75px">
                        <col style="width: 90px">
                        <col style="width: 180px">
                        <col style="width: 80px">
                        <col style="width: 300px">
                    </colgroup>
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">Código</th>
                            <th class="py-2">Actividad</th>
                            <th class="py-2">Responsable</th>
                            <th class="py-2">Año</th>
                            <th class="py-2">Avance</th>
                            <th class="py-2">Estado</th>
                            <th class="py-2">Activa</th>
                            <th class="py-2 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($actividades as $actividad)
                            <tr class="border-b">
                                <td class="py-2">{{ $actividad->codigo }}</td>
                                <td class="py-2">
                                    <div>{{ $actividad->nombre }}</div>
                                    <div class="text-sm text-gray-500">{{ $actividad->plan->nombre }}</div>
                                </td>
                                <td class="py-2">{{ $actividad->responsable }}</td>
                                <td class="py-2">{{ $actividad->anio }}</td>
                                <td class="py-2">{{ $actividad->avance }}%</td>
                                <td class="py-2">
                                    <div>{{ \App\Models\ActividadOperativa::ESTADOS[$actividad->estado] }}</div>
                                    @if($actividad->comentario_revision)
                                        <div class="text-sm text-gray-500">{{ $actividad->comentario_revision }}</div>
                                    @endif
                                </td>
                                <td class="py-2">{{ $actividad->activo ? 'Activa' : 'Inactiva' }}</td>
                                <td class="py-2 poa-actions-cell"><div class="poa-actions">
                                    @if (auth()->user()->canManagePlanning() && $actividad->activo && $actividad->puedeEditar())
                                        <a href="{{ route('actividades-operativas.edit', $actividad) }}" class="btn poa-action">Editar</a>
                                        <form method="POST" action="{{ route('actividades-operativas.enviar-revision', $actividad) }}">
                                            @csrf
                                            <button class="btn poa-action">Enviar a revisión</button>
                                        </form>
                                    @endif
                                    @if ((auth()->user()->isAdmin() || auth()->user()->isAprobador()) && $actividad->estado === 'en_revision')
                                        <a href="{{ route('actividades-operativas.revisar', $actividad) }}" class="btn poa-action">Revisar</a>
                                    @endif
                                    @if (auth()->user()->canManagePlanning() && count($actividad->transicionesPermitidas()))
                                        <form method="POST" action="{{ route('actividades-operativas.cambiar-estado', $actividad) }}" class="poa-transition">
                                            @csrf
                                            <select name="estado" class="poa-state-select">
                                                @foreach($actividad->transicionesPermitidas() as $estado)
                                                    <option value="{{ $estado }}">{{ \App\Models\ActividadOperativa::ESTADOS[$estado] }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-green poa-action">Cambiar</button>
                                        </form>
                                    @endif
                                    @if (auth()->user()->canManagePlanning() && $actividad->activo && $actividad->estado === 'borrador')
                                        <form method="POST" action="{{ route('actividades-operativas.destroy', $actividad) }}" onsubmit="return confirm('¿Deseas desactivar esta actividad?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn poa-action">Desactivar</button>
                                        </form>
                                    @endif
                                </div></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-4 text-center text-gray-500">No hay actividades operativas registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
