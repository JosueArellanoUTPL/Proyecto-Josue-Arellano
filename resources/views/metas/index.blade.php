<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Metas
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('metas.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded shadow transition">
                    + Nueva Meta
                </a>
            </div>

            <div class="bg-white shadow rounded p-4 overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">ID</th>
                            <th class="py-2">Código</th>
                            <th class="py-2">Nombre</th>
                            <th class="py-2">Plan</th>
                            <th class="py-2">Objetivo</th>
                            <th class="py-2">Activo</th>
                            <th class="py-2 text-center w-56">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($metas as $meta)
                            <tr class="border-b">
                                <td class="py-2">{{ $meta->id }}</td>
                                <td class="py-2">{{ $meta->codigo }}</td>
                                <td class="py-2">{{ $meta->nombre }}</td>
                                <td class="py-2">{{ $meta->plan->nombre ?? '-' }}</td>
                                <td class="py-2">
                                    @if($meta->valor_objetivo !== null)
                                        {{ $meta->valor_objetivo }} {{ $meta->unidad }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-2">{{ $meta->activo ? 'Sí' : 'No' }}</td>

                                <td class="py-2">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('metas.edit', $meta->id) }}"
                                           class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-black rounded transition">
                                            Editar
                                        </a>

                                        <form method="POST"
                                              action="{{ route('metas.destroy', $meta->id) }}"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar esta meta?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded transition">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-gray-500">
                                    No hay metas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
