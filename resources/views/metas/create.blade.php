<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Meta
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded p-6">
                <x-form-errors />

                <form method="POST" action="{{ route('metas.store') }}">
                    {{-- Formulario de datos. --}}
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
                        <label class="block mb-1">Plan</label>
                        <select name="plan_id" class="w-full border rounded px-3 py-2">
                            <option value="">Seleccione</option>
                            @foreach ($planes as $plan)
                                <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->codigo }} - {{ $plan->nombre }}
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

                        <a href="{{ route('metas.index') }}"
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-black font-semibold rounded transition">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
