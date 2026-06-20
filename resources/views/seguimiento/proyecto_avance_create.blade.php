<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar avance de Proyecto
        </h2>
    </x-slot>
    {{-- Los estilos reutilizables de esta vista ahora estan en resources/css/app.css --}}

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                <div class="flex justify-between items-start gap-4 flex-wrap">
                    <div>
                        <div class="title">{{ $proyecto->nombre }}</div>
                        <div class="muted" style="margin-top:6px;">
                            Entidad: <strong>{{ $proyecto->entidad->nombre ?? '—' }}</strong> ·
                            Programa: <strong>{{ $proyecto->programa->nombre ?? '—' }}</strong>
                        </div>
                    </div>

                    <a class="btn" href="{{ route('seguimiento.proyecto.show', $proyecto->id) }}">← Volver</a>
                </div>

                @if ($errors->any())
                    <div class="card" style="margin-top:16px; border-color:#f3b4b4; background:#fff5f5;">
                        <div class="title">Revisar campos</div>
                        <ul class="muted" style="margin-top:8px; list-style:disc; padding-left:18px;">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="card" style="margin-top:16px;" method="POST"
                      action="{{ route('proyectos.avance.store', $proyecto->id) }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div style="display:grid; gap:12px;">
                        <div>
                            <label class="label">Fecha</label>
                            <input type="date" name="fecha" class="input"
                                   value="{{ old('fecha', now()->toDateString()) }}">
                            @error('fecha') <div class="err">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="label">Porcentaje de avance (0 a 100)</label>
                            <input type="number" step="0.01" min="0" max="100"
                                   name="porcentaje_avance"
                                   class="input"
                                   value="{{ old('porcentaje_avance') }}">
                            @error('porcentaje_avance') <div class="err">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="label">Comentario</label>
                            <textarea name="comentario" rows="3" class="input"
                                      placeholder="Descripción breve del avance...">{{ old('comentario') }}</textarea>
                            @error('comentario') <div class="err">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="label">Evidencia (opcional)</label>

                            {{-- En creación se permite subir 1 evidencia.
                                 Si luego se desea agregar más, se hace desde el historial del avance. --}}
                            <input type="file" name="evidencias[]" class="input" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="muted" style="margin-top:6px;">
                                Se puede agregar más evidencias después desde el avance.
                                Máx 5MB.
                            </div>

                            @error('evidencias') <div class="err">{{ $message }}</div> @enderror
                            @error('evidencias.*') <div class="err">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="flex gap-10 mt-6">
                        <button type="submit" class="btn" style="background:#eef7f5; color:#225c52;">
                            Guardar avance
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
