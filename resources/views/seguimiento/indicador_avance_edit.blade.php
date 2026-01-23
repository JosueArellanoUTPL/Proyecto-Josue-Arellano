<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Avance de Indicador
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                <div class="title">
                    {{ $avance->indicador->codigo }} — {{ $avance->indicador->nombre }}
                </div>

                <div class="muted" style="margin-top:6px;">
                    Meta: {{ $avance->indicador->meta->nombre }}
                </div>

                <form method="POST"
                      action="{{ route('indicadores.avance.update', $avance->id) }}"
                      enctype="multipart/form-data"
                      style="margin-top:20px;">
                    @csrf
                    @method('PUT')

                    <div class="card">
                        <div class="mb-4">
                            <label class="block mb-1 font-semibold">Fecha</label>
                            <input type="date" name="fecha"
                                   value="{{ old('fecha', $avance->fecha->format('Y-m-d')) }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>

                        <div class="mb-4">
                            <label class="block mb-1 font-semibold">Valor reportado</label>
                            <input type="number" step="0.01" name="valor_reportado"
                                   value="{{ old('valor_reportado', $avance->valor_reportado) }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>

                        <div class="mb-4">
                            <label class="block mb-1 font-semibold">Comentario</label>
                            <textarea name="comentario" rows="3"
                                      class="w-full border rounded px-3 py-2">{{ old('comentario', $avance->comentario) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-1 font-semibold">Nueva evidencia (opcional)</label>
                            <input type="file" name="evidencia" class="w-full">
                        </div>

                        <div class="flex gap-3">
                            <button class="btn btn-blue" type="submit">Actualizar</button>
                            <a href="{{ route('seguimiento.meta.show', $avance->indicador->meta_id) }}"
                               class="btn">Cancelar</a>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
