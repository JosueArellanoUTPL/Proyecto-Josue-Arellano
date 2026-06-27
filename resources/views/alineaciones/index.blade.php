<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Alineaciones
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                <x-success-message />

                <div class="row">
                    <div>
                        <div class="title">Alineaciones estrategicas</div>
                        <div class="muted" style="margin-top:6px;">
                            Relacion de metas con ODS, PND y objetivos estrategicos.
                        </div>
                    </div>

                    <a href="{{ route('alineaciones.create') }}" class="btn">Nueva alineacion</a>
                </div>

                {{-- Filtros. --}}
                <form method="GET" action="{{ route('alineaciones.index') }}" class="card alignment-filter" data-alignment-filter>
                    <div>
                        <label class="label" for="entidad_id">Entidad</label>
                        <select class="input" id="entidad_id" name="entidad_id" data-entity-filter>
                            <option value="">Todas las entidades</option>
                            @foreach ($entidades as $entidad)
                                <option value="{{ $entidad->id }}" @selected((string) $entidadSeleccionada === (string) $entidad->id)>
                                    {{ $entidad->codigo }} - {{ $entidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label" for="meta_id">Meta</label>
                        <select class="input" id="meta_id" name="meta_id" data-meta-filter @disabled(!$entidadSeleccionada)>
                            <option value="">{{ $entidadSeleccionada ? 'Todas las metas' : 'Seleccione una entidad' }}</option>
                            @foreach ($metas as $meta)
                                <option value="{{ $meta->id }}" @selected((string) $metaSeleccionada === (string) $meta->id)>
                                    {{ $meta->codigo }} - {{ $meta->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alignment-filter__actions">
                        <button class="btn" type="submit">Filtrar</button>
                        <a class="btn" href="{{ route('alineaciones.index') }}">Limpiar</a>
                    </div>
                </form>

                {{-- Listado. --}}
                <div class="card alignment-panel">
                    <div class="alignment-list-header" aria-hidden="true">
                        <span>Meta</span>
                        <span>Instrumentos</span>
                        <span>Acciones</span>
                    </div>

                    <div class="alignment-list">
                        @forelse ($alineaciones as $alineacion)
                            <div class="alignment-list-row">
                                <div class="alignment-meta">
                                    <strong>{{ $alineacion->meta->codigo ?? 'META' }} - {{ $alineacion->meta->nombre ?? '-' }}</strong>
                                    <span>{{ $alineacion->meta->plan->entidad->nombre ?? '-' }}</span>
                                    <span>{{ $alineacion->activo ? 'Activa' : 'Inactiva' }}</span>
                                </div>

                                <div class="alignment-instruments">
                                    <div><span>ODS</span><strong>{{ $alineacion->ods->codigo ?? 'Sin asignar' }}</strong></div>
                                    <div><span>PND</span><strong>{{ $alineacion->pdn->codigo ?? 'Sin asignar' }}</strong></div>
                                    <div><span>OE</span><strong>{{ $alineacion->objetivoEstrategico->nombre ?? 'Sin asignar' }}</strong></div>
                                </div>

                                <div class="alignment-actions">
                                    <a href="{{ route('alineaciones.edit', $alineacion) }}" class="alignment-action">Editar</a>
                                    @if($alineacion->activo)
                                        <form method="POST" action="{{ route('alineaciones.destroy', $alineacion) }}"
                                              onsubmit="return confirm('Desactivar esta alineacion?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="alignment-action alignment-action--danger">Desactivar</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="alignment-empty">
                                No hay alineaciones para los filtros seleccionados.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Al cambiar de entidad, limpia la meta anterior y actualiza el listado.
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('[data-alignment-filter]');
            const entity = form?.querySelector('[data-entity-filter]');
            const meta = form?.querySelector('[data-meta-filter]');

            entity?.addEventListener('change', function () {
                if (meta) meta.value = '';
                form.submit();
            });

            meta?.addEventListener('change', function () {
                form.submit();
            });
        });
    </script>
</x-app-layout>
