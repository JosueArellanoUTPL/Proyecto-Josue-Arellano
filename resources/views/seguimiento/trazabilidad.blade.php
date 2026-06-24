<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Matriz de Trazabilidad Institucional
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">

                <div class="row">
                    <div>
                        <div class="title">Trazabilidad Estratégica</div>
                        <div class="muted" style="margin-top:6px;">
                            Visualiza cómo las metas se alinean con ODS, PND y objetivos estratégicos.
                        </div>
                    </div>
                    <a class="btn" href="{{ route('dashboard') }}">← Dashboard</a>
                </div>

                {{-- Filtros. --}}
                <form class="card trace-filters" method="GET" action="{{ route('seguimiento.trazabilidad') }}">
                    <div class="trace-filters__top">
                        <div>
                            <div class="title">Filtros</div>
                            <div class="muted">Busca relaciones sin modificar información.</div>
                        </div>

                    </div>

                    <div class="trace-filters__fields">
                            <div>
                                <label class="label">Entidad</label>
                                <select class="input" name="entidad_id">
                                    <option value="">(Todas)</option>
                                    @foreach($entidades as $entidad)
                                        <option value="{{ $entidad->id }}" @selected((string)$entidadId === (string)$entidad->id)>
                                            {{ $entidad->codigo ?? '' }}{{ isset($entidad->codigo) ? ' - ' : '' }}{{ $entidad->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">Meta</label>
                                <select class="input" name="meta_id">
                                    <option value="">(Todas)</option>
                                    @foreach($metas as $meta)
                                        <option value="{{ $meta->id }}" @selected((string)$metaId === (string)$meta->id)>
                                            {{ $meta->codigo }} - {{ $meta->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">ODS</label>
                                <select class="input" name="ods_id">
                                    <option value="">(Cualquiera)</option>
                                    @foreach($ods as $objetivoOds)
                                        <option value="{{ $objetivoOds->id }}" @selected((string)$odsId === (string)$objetivoOds->id)>
                                            {{ $objetivoOds->codigo }} - {{ $objetivoOds->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">PND</label>
                                <select class="input" name="pdn_id">
                                    <option value="">(Cualquiera)</option>
                                    @foreach($pdns as $pdn)
                                        <option value="{{ $pdn->id }}" @selected((string)$pdnId === (string)$pdn->id)>
                                            {{ $pdn->codigo }} - {{ $pdn->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">Objetivo Estratégico</label>
                                <select class="input" name="objetivo_estrategico_id">
                                    <option value="">(Cualquiera)</option>
                                    @foreach($objetivos as $objetivo)
                                        <option value="{{ $objetivo->id }}" @selected((string)$objetivoId === (string)$objetivo->id)>
                                            {{ $objetivo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">Mostrar</label>
                                <select class="input" name="solo_activas">
                                    <option value="1" @selected((string)$soloActivas === '1')>Solo activas</option>
                                    <option value="0" @selected((string)$soloActivas === '0')>Todas</option>
                                </select>
                            </div>

                        <div class="trace-filters__actions">
                            <button class="btn trace-filters__apply" type="submit">Aplicar filtros</button>
                            <a class="btn" href="{{ route('seguimiento.trazabilidad') }}">Limpiar</a>
                        </div>
                    </div>
                </form>

                <div class="card" style="margin-top:14px;">
                    <div class="title">Registros</div>
                    <div class="muted" style="margin-top:6px;">
                        Resultados: <strong>{{ $alineaciones->count() }}</strong>
                    </div>

                    <div class="list">
                        @forelse($alineaciones as $alineacion)
                            <div class="item">
                                <div class="row">
                                    <div>
                                        <div class="strong">
                                            {{ $alineacion->meta->codigo ?? 'META' }} — {{ $alineacion->meta->nombre ?? '-' }}
                                        </div>
                                        <div class="small" style="margin-top:4px;">
                                            Entidad: {{ $alineacion->meta->plan->entidad->nombre ?? '—' }} ·
                                            Plan: {{ $alineacion->meta->plan->codigo ?? '' }}
                                        </div>
                                    </div>

                                    {{-- Estado de la alineacion. --}}
                                    <span class="trace-status {{ $alineacion->activo ? 'is-active' : 'is-inactive' }}">
                                        {{ $alineacion->activo ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>

                                {{-- Instrumentos relacionados. --}}
                                <div class="trace-instruments">
                                    <div class="trace-instrument">
                                        <span>ODS</span>
                                        @if($alineacion->ods)
                                            <strong>{{ $alineacion->ods->codigo }}</strong>
                                            <div>{{ $alineacion->ods->nombre }}</div>
                                        @else
                                            <div class="muted">Sin vinculacion</div>
                                        @endif
                                    </div>

                                    <div class="trace-instrument">
                                        <span>PND</span>
                                        @if($alineacion->pdn)
                                            <strong>{{ $alineacion->pdn->codigo }}</strong>
                                            <div>{{ $alineacion->pdn->nombre }}</div>
                                        @else
                                            <div class="muted">Sin vinculacion</div>
                                        @endif
                                    </div>

                                    <div class="trace-instrument">
                                        <span>Objetivo estrategico</span>
                                        @if($alineacion->objetivoEstrategico)
                                            <strong>{{ $alineacion->objetivoEstrategico->codigo }}</strong>
                                            <div>{{ $alineacion->objetivoEstrategico->nombre }}</div>
                                        @else
                                            <div class="muted">Sin vinculacion</div>
                                        @endif
                                    </div>
                                </div>

                                <div style="margin-top:10px;">
                                    <a class="btn" href="{{ route('seguimiento.meta.show', $alineacion->meta_id) }}">Ver meta →</a>
                                </div>
                            </div>
                        @empty
                            <div class="muted">No hay registros con los filtros actuales.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
