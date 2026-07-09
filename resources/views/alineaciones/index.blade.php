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

                {{-- Listado. --}}
                <div class="card alignment-panel">
                    <div class="alignment-list-header" aria-hidden="true">
                        <span>Meta</span>
                        <span>Instrumentos</span>
                        <span>Acciones</span>
                    </div>

                    {{-- Lista de alineaciones encontradas. --}}
                    <div class="alignment-list">
                        @forelse ($alineaciones as $alineacion)
                            <div class="alignment-list-row">
                                <div class="alignment-meta">
                                    <strong>{{ $alineacion->meta->codigo ?? 'META' }} - {{ $alineacion->meta->nombre ?? '-' }}</strong>
                                    <span>{{ $alineacion->meta->plan->entidad->nombre ?? '-' }}</span>
                                    <span>{{ $alineacion->activo ? 'Activa' : 'Inactiva' }}</span>
                                </div>

                                {{-- Muestra ODS, PND y OE a la derecha. --}}
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
                                No hay alineaciones registradas.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

</x-app-layout>
