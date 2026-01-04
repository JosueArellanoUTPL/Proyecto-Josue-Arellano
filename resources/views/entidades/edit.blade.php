<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Entidad
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

                <form method="POST" action="{{ route('entidades.update', $entidad->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block mb-1">Nombre</label>
                        <input name="nombre" value="{{ old('nombre', $entidad->nombre) }}"
                               class="w-full border rounded px-3 py-2" />
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Descripción</label>
                        <textarea name="descripcion"
                                  class="w-full border rounded px-3 py-2"
                                  rows="3">{{ old('descripcion', $entidad->descripcion) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="activo" {{ old('activo', $entidad->activo) ? 'checked' : '' }}>
                            <span>Activo</span>
                        </label>
                    </div>

                    <div class="flex gap-2">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded" type="submit">
                            Actualizar
                        </button>
                        <a class="px-4 py-2 bg-gray-400 text-white rounded"
                           href="{{ route('entidades.index') }}">
                            Volver
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
