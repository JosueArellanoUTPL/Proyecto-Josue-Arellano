<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Proyecto
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded p-6">
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded">
                        <ul class="list-disc ml-5 text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('proyectos.update', $proyecto->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Nombre</label>
                        <input name="nombre" value="{{ old('nombre', $proyecto->nombre) }}"
                               class="w-full border rounded px-3 py-2" />
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Descripción</label>
                        <textarea name="descripcion"
                                  class="w-full border rounded px-3 py-2"
                                  rows="3">{{ old('descripcion', $proyecto->descripcion) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Entidad</label>
                        <select name="entidad_id" class="w-full border rounded px-3 py-2">
                            @foreach ($entidades as $entidad)
                                <option value="{{ $entidad->id }}"
                                    {{ old('entidad_id', $proyecto->entidad_id) == $entidad->id ? 'selected' : '' }}>
                                    {{ $entidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Programa</label>
                        <select name="programa_id" class="w-full border rounded px-3 py-2">
                            @foreach ($programas as $programa)
                                <option value="{{ $programa->id }}"
                                    {{ old('programa_id', $proyecto->programa_id) == $programa->id ? 'selected' : '' }}>
                                    {{ $programa->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="activo"
                                   {{ old('activo', $proyecto->activo) ? 'checked' : '' }}>
                            <span>Activo</span>
                        </label>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded transition">
                            Actualizar
                        </button>

                        <a href="{{ route('proyectos.index') }}"
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-black font-semibold rounded transition">
                            Volver
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
