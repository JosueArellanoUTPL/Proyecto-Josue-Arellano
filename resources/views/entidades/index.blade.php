<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Entidades
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
              <a href="{{ route('entidades.create') }}"
   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-gray font-semibold rounded shadow transition">
    + Nueva Entidad
</a>
            </div>

            <div class="bg-white shadow rounded p-4 overflow-x-auto">
                <table class="w-full">
                   <thead>
    <tr class="text-left border-b">
        <th class="py-2">ID</th>
        <th class="py-2">Nombre</th>
        <th class="py-2">Activo</th>
        <th class="py-2 text-center w-56">Acciones</th>
    </tr>
</thead>

                    <tbody>
                        @forelse ($entidades as $entidad)
                            <tr class="border-b">
                                <td class="py-2">{{ $entidad->id }}</td>
                                <td class="py-2">{{ $entidad->nombre }}</td>
                                <td class="py-2">{{ $entidad->activo ? 'Sí' : 'No' }}</td>

                                {{-- Acciones centradas --}}
                                <td class="py-2">
                                    <div class="flex justify-center gap-2">
                                        <a class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition"
                                           href="{{ route('entidades.edit', $entidad->id) }}">
                                            Editar
                                        </a>

                                        <form method="POST" action="{{ route('entidades.destroy', $entidad->id) }}"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar esta entidad?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded transition"
                                                    type="submit">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 text-center text-gray-500" colspan="4">
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
