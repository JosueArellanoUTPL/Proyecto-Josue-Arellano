<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Usuarios
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-success-message />

            <div class="mb-4">
                <a href="{{ route('usuarios.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded shadow transition">
                    + Nuevo Usuario
                </a>
            </div>

            {{-- Seccion de tabla. --}}
            <div class="bg-white shadow rounded p-4 overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">ID</th>
                        <th class="py-2">Nombre</th>
                        <th class="py-2">Email</th>
                        <th class="py-2">Rol</th>
                        <th class="py-2">Estado</th>
                        <th class="py-2 text-center w-56">Acciones</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr class="border-b">
                            <td class="py-2">{{ $usuario->id }}</td>
                            <td class="py-2">{{ $usuario->name }}</td>
                            <td class="py-2">{{ $usuario->email }}</td>
                            <td class="py-2">{{ $usuario->roleLabel() }}</td>
                            <td class="py-2">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</td>

                            <td class="py-2">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('usuarios.edit', $usuario->id) }}"
                                       class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-black rounded transition">
                                        Editar
                                    </a>

                                    <form method="POST"
                                          action="{{ route('usuarios.destroy', $usuario->id) }}"
                                          onsubmit="return confirm('¿Seguro que deseas desactivar este usuario?');">
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
                            <td class="py-4 text-center text-gray-500" colspan="6">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{-- Encabezado y boton crear. --}}
                <div class="mt-4">
                    {{ $usuarios->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
