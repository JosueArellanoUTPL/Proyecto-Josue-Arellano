<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Plan
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

                <form method="POST" action="{{ route('plans.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-1">Código</label>
                        <input name="codigo"
                               value="{{ old('codigo') }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Nombre</label>
                        <input name="nombre"
                               value="{{ old('nombre') }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Descripción</label>
                        <textarea name="descripcion" rows="3"
                                  class="w-full border rounded px-3 py-2">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-1">Año inicio</label>
                            <input type="number" name="anio_inicio"
                                   value="{{ old('anio_inicio') }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label class="block mb-1">Año fin</label>
                            <input type="number" name="anio_fin"
                                   value="{{ old('anio_fin') }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>
                    </div>

                    {{-- NUEVO: Entidad --}}
                    <div class="mb-4">
                        <label class="block mb-1">Entidad</label>
                        <select name="entidad_id" class="w-full border rounded px-3 py-2">
                            <option value="">Seleccione</option>
                            @foreach ($entidades as $entidad)
                                <option value="{{ $entidad->id }}"
                                    {{ old('entidad_id') == $entidad->id ? 'selected' : '' }}>
                                    {{ $entidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">PND / PDN</label>
                        <select name="pdn_id" class="w-full border rounded px-3 py-2">
                            <option value="">Seleccione</option>
                            @foreach ($pdns as $pdn)
                                <option value="{{ $pdn->id }}"
                                    {{ old('pdn_id') == $pdn->id ? 'selected' : '' }}>
                                    {{ $pdn->codigo }} - {{ $pdn->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="activo" value="1" checked>
                            <span>Activo</span>
                        </label>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded transition">
                            Guardar
                        </button>

                        <a href="{{ route('plans.index') }}"
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-black font-semibold rounded transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
