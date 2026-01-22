<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar avance de Proyecto
        </h2>
    </x-slot>

    <style>
        :root{
            --beige:#f5f1ea; --card:#ffffff; --border:#cfd5dd; --border-soft:#d9dee6;
            --blue:#8faadc; --green:#9fd3c7; --orange:#f4c095;
            --text:#1f2937; --muted:#6b7280;
        }
        .wrap{ background:var(--beige); border-radius:20px; padding:28px; border:1px solid var(--border-soft); }
        .card{ background:var(--card); border-radius:18px; padding:18px; border:1px solid var(--border); }
        .title{ font-weight:700; font-size:18px; color:var(--text); }
        .muted{ color:var(--muted); font-size:13px; }
        .btn{ display:inline-flex; align-items:center; justify-content:center; height:34px; padding:0 14px; border-radius:10px;
              border:1px solid var(--border); font-weight:700; font-size:13px; text-decoration:none; background:#f0f4fb; color:#365a99; }
        .btn:hover{ background:#e6eefc; }
        .input{ width:100%; border:1px solid var(--border); border-radius:12px; padding:10px 12px; background:#fff; }
        .label{ font-size:13px; font-weight:700; color:var(--text); margin-bottom:6px; display:block; }
        .err{ margin-top:8px; font-size:13px; color:#b91c1c; }
    </style>

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
                        <div class="title">Revisa los campos</div>
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
                            <input type="date" name="fecha" class="input" value="{{ old('fecha', now()->toDateString()) }}">
                            @error('fecha') <div class="err">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="label">Porcentaje de avance (0 a 100)</label>
                            <input type="number" step="0.01" min="0" max="100" name="porcentaje_avance"
                                   class="input" value="{{ old('porcentaje_avance') }}">
                            @error('porcentaje_avance') <div class="err">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="label">Comentario (opcional)</label>
                            <textarea name="comentario" rows="3" class="input"
                                      placeholder="Describe qué se logró en este avance...">{{ old('comentario') }}</textarea>
                            @error('comentario') <div class="err">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="label">Evidencias (puedes subir varias)</label>
                            <input type="file" name="evidencias[]" class="input" multiple>
                            <div class="muted" style="margin-top:6px;">Imágenes, PDF, documentos. Máx 5MB por archivo.</div>
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
