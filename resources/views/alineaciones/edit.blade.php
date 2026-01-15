<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Alineación
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

                <form method="POST" action="{{ route('alineaciones.update', $alineacion->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block mb-1">Meta (obligatorio)</label>
                        <select name="meta_id" class="w-full border rounded px-3 py-2">
                            @foreach ($metas as $m)
                                <option value="{{ $m->id }}" {{ old('meta_id', $alineacion->meta_id) == $m->id ? 'selected' : '' }}>
                                    {{ $m->codigo }} - {{ $m->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Indicador (opcional)</label>
                        <select name="indicador_id" class="w-full border rounded px-3 py-2">
                            <option value="">(Ninguno)</option>
                            @foreach ($indicadores as $i)
                                <option value="{{ $i->id }}" {{ old('indicador_id', $alineacion->indicador_id) == $i->id ? 'selected' : '' }}>
                                    {{ $i->codigo }} - {{ $i->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">ODS (opcional)</label>
                        <select name="ods_id" class="w-full border rounded px-3 py-2">
                            <option value="">(Ninguno)</option>
                            @foreach ($ods as $o)
                                <option value="{{ $o->id }}" {{ old('ods_id', $alineacion->ods_id) == $o->id ? 'selected' : '' }}>
                                    {{ $o->codigo }} - {{ $o->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">PND/PDN (opcional)</label>
                        <select name="pdn_id" class="w-full border rounded px-3 py-2">
                            <option value="">(Ninguno)</option>
                            @foreach ($pdns as $p)
                                <option value="{{ $p->id }}" {{ old('pdn_id', $alineacion->pdn_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->codigo }} - {{ $p->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Objetivo Estratégico (opcional)</label>
                        <select name="objetivo_estrategico_id" class="w-full border rounded px-3 py-2">
                            <option value="">(Ninguno)</option>
                            @foreach ($objetivos as $obj)
                                <option value="{{ $obj->id }}" {{ old('objetivo_estrategico_id', $alineacion->objetivo_estrategico_id) == $obj->id ? 'selected' : '' }}>
                                    {{ $obj->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="activo" value="1"
                                   {{ old('activo', $alineacion->activo) ? 'checked' : '' }}>
                            <span>Activo</span>
                        </label>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded transition">
                            Actualizar
                        </button>

                        <a href="{{ route('alineaciones.index') }}"
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-black font-semibold rounded transition">
                            Volver
                        </a>
                    </div>

                    <p class="mt-4 text-sm text-gray-500">
                        Nota: debes seleccionar al menos un instrumento (ODS, PND/PDN u Objetivo Estratégico).
                    </p>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
