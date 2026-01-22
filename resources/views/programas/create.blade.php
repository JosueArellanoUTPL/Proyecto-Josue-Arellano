<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Programa
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded p-6" style="background:#ffffff; border:1px solid #d9dee6;">
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('programas.store') }}">
                    @csrf

                    {{-- ENTIDAD --}}
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Entidad</label>
                        <select name="entidad_id" class="w-full border rounded px-3 py-2"
                                style="border-color:#cfd5dd; background:#fff;">
                            <option value="">-- Seleccionar --</option>
                            @foreach($entidades as $entidad)
                                <option value="{{ $entidad->id }}"
                                    {{ old('entidad_id') == $entidad->id ? 'selected' : '' }}>
                                    {{ $entidad->codigo ?? '' }}{{ isset($entidad->codigo) ? ' - ' : '' }}{{ $entidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('entidad_id')
                            <div class="mt-2 text-sm text-red-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Nombre</label>
                        <input name="nombre"
                               value="{{ old('nombre') }}"
                               class="w-full border rounded px-3 py-2"
                               style="border-color:#cfd5dd;">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Descripción</label>
                        <textarea name="descripcion"
                                  rows="3"
                                  class="w-full border rounded px-3 py-2"
                                  style="border-color:#cfd5dd;">{{ old('descripcion') }}</textarea>
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

                        <a href="{{ route('programas.index') }}"
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
