<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Indicador
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

                <form method="POST" action="{{ route('indicadores.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-1">Código</label>
                        <input name="codigo" value="{{ old('codigo') }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Nombre</label>
                        <input name="nombre" value="{{ old('nombre') }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Descripción</label>
                        <textarea name="descripcion" rows="3"
                                  class="w-full border rounded px-3 py-2">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Meta</label>
                        <select name="meta_id" class="w-full border rounded px-3 py-2">
                            <option value="">Seleccione</option>
                            @foreach ($metas as $meta)
                                <option value="{{ $meta->id }}" {{ old('meta_id') == $meta->id ? 'selected' : '' }}>
                                    {{ $meta->codigo }} - {{ $meta->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block mb-1">Línea base (opcional)</label>
                            <input type="number" step="0.01" name="linea_base"
                                   value="{{ old('linea_base') }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label class="block mb-1">Valor meta (opcional)</label>
                            <input type="number" step="0.01" name="valor_meta"
                                   value="{{ old('valor_meta') }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label class="block mb-1">Unidad (opcional)</label>
                            <input name="unidad"
                                   placeholder="Ej: %, USD, unidades"
                                   value="{{ old('unidad') }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>
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

                        <a href="{{ route('indicadores.index') }}"
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-black font-semibold rounded transition">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
