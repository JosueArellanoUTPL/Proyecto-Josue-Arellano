<x-app-layout>
    {{-- Seccion de encabezado. --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Entidades
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-success-message />

            <div class="mb-4">
                <a href="{{ route('entidades.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded shadow transition">
                    + Nueva Entidad
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
                            <th class="py-2">Activo</th>
                            <th class="py-2 text-center w-56">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($entidades as $entidad)
                            <tr class="border-b">
                                <td class="py-2">{{ $entidad->id }}</td>
                                <td class="py-2">{{ $entidad->codigo }}</td>
                                <td class="py-2">{{ $entidad->nombre }}</td>
                                <td class="py-2">{{ $entidad->activo ? 'Sí' : 'No' }}</td>

                                <td class="py-2">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('entidades.edit', $entidad->id) }}"
                                           class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-black rounded transition">
                                            Editar
                                        </a>

                                        <form method="POST"
                                              action="{{ route('entidades.destroy', $entidad->id) }}"
                                              onsubmit="return confirm('¿Seguro que deseas desactivar esta entidad?');">
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
                                <td class="py-4 text-center text-gray-500" colspan="5">
                                    No hay entidades registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $entidades->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
