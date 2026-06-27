<x-app-layout>
    {{-- Seccion de encabezado. --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Usuario
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6">

                <x-form-errors />

                <form method="POST" action="{{ route('usuarios.store') }}">
                    {{-- Formulario de datos. --}}
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-1">Nombre</label>
                        <input name="name" value="{{ old('name') }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Email</label>
                        <input name="email" type="email" value="{{ old('email') }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Rol</label>
                        <select name="role" class="w-full border rounded px-3 py-2">
                            @foreach(\App\Models\User::ROLE_LABELS as $role => $label)
                                <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Contraseña</label>
                        <input name="password" type="password"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Confirmar contraseña</label>
                        <input name="password_confirmation" type="password"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded transition">
                            Guardar
                        </button>

                        <a href="{{ route('usuarios.index') }}"
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded transition">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
