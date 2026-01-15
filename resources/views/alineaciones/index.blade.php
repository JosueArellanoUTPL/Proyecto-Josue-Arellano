<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Alineaciones
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
                <a href="{{ route('alineaciones.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded shadow transition">
                    + Nueva Alineación
                </a>
            </div>

            <div class="bg-white shadow rounded p-4 overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">ID</th>
                            <th class="py-2">Meta</th>
                            <th class="py-2">Indicador</th>
                            <th class="py-2">ODS</th>
                            <th class="py-2">PND/PDN</th>
                            <th class="py-2">Obj. Estratégico</th>
                            <th class="py-2">Activo</th>
                            <th class="py-2 text-center w-56">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($alineaciones as $a)
                            <tr class="border-b">
                                <td class="py-2">{{ $a->id }}</td>
                                <td class="py-2">{{ $a->meta->nombre ?? '-' }}</td>
                                <td class="py-2">{{ $a->indicador->nombre ?? '-' }}</td>
                                <td class="py-2">{{ $a->ods->codigo ?? '-' }} {{ $a->ods->nombre ?? '' }}</td>
                                <td class="py-2">{{ $a->pdn->codigo ?? '-' }} {{ $a->pdn->nombre ?? '' }}</td>
                                <td class="py-2">{{ $a->objetivoEstrategico->nombre ?? '-' }}</td>
                                <td class="py-2">{{ $a->activo ? 'Sí' : 'No' }}</td>

                                <td class="py-2">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('alineaciones.edit', $a->id) }}"
                                           class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-black rounded transition">
                                            Editar
                                        </a>

                                        <form method="POST"
                                              action="{{ route('alineaciones.destroy', $a->id) }}"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar esta alineación?');">
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
                                <td colspan="8" class="py-4 text-center text-gray-500">
                                    No hay alineaciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
