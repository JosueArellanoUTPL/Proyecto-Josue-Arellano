<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Proyecto
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded p-6">
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('proyectos.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-1">Nombre</label>
                        <input name="nombre" value="{{ old('nombre') }}"
                               class="w-full border rounded px-3 py-2" />
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Descripción</label>
                        <textarea name="descripcion"
                                  class="w-full border rounded px-3 py-2"
                                  rows="3">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Entidad</label>
                        <select name="entidad_id" class="w-full border rounded px-3 py-2">
                            <option value="">Seleccione una entidad</option>
                            @foreach ($entidades as $entidad)
                                <option value="{{ $entidad->id }}"
                                    {{ old('entidad_id') == $entidad->id ? 'selected' : '' }}>
                                    {{ $entidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Programa</label>
                        <select name="programa_id" class="w-full border rounded px-3 py-2">
                            <option value="">Seleccione un programa</option>
                            @foreach ($programas as $programa)
                                <option value="{{ $programa->id }}"
                                    {{ old('programa_id') == $programa->id ? 'selected' : '' }}>
                                    {{ $programa->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="activo" {{ old('activo', true) ? 'checked' : '' }}>
                            <span>Activo</span>
                        </label>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded transition">
                            Guardar
                        </button>

                        <a href="{{ route('proyectos.index') }}"
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

