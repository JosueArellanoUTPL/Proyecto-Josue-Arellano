<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Indicadores
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-success-message />

            <div class="mb-4">
                <a href="{{ route('indicadores.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded shadow transition">
                    + Nuevo Indicador
                </a>
            </div>

            {{-- Seccion de tabla. --}}
            <div class="bg-white shadow rounded p-4 overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">ID</th>
                            <th class="py-2">Código</th>
                            <th class="py-2">Nombre</th>
                            <th class="py-2">Meta</th>
                            <th class="py-2">Línea base</th>
                            <th class="py-2">Valor meta</th>
                            <th class="py-2">Activo</th>
                            <th class="py-2 text-center w-56">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($indicadores as $indicador)
                            <tr class="border-b">
                                <td class="py-2">{{ $indicador->id }}</td>
                                <td class="py-2">{{ $indicador->codigo }}</td>
                                <td class="py-2">{{ $indicador->nombre }}</td>
                                <td class="py-2">{{ $indicador->meta->nombre ?? '-' }}</td>

                                <td class="py-2">
                                    {{ $indicador->linea_base !== null ? $indicador->linea_base . ' ' . $indicador->unidad : '-' }}
                                </td>

                                <td class="py-2">
                                    {{ $indicador->valor_meta !== null ? $indicador->valor_meta . ' ' . $indicador->unidad : '-' }}
                                </td>

                                <td class="py-2">{{ $indicador->activo ? 'Sí' : 'No' }}</td>

                                <td class="py-2">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('indicadores.edit', $indicador->id) }}"
                                           class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-black rounded transition">
                                            Editar
                                        </a>

                                        <form method="POST"
                                              action="{{ route('indicadores.destroy', $indicador->id) }}"
                                              onsubmit="return confirm('¿Seguro que deseas desactivar este indicador?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded transition">
                                                Desactivar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-4 text-center text-gray-500">
                                    No hay indicadores registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
