<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Matriz de Trazabilidad Institucional
        </h2>
    </x-slot>
    {{-- Los estilos reutilizables de esta vista ahora estan en resources/css/app.css --}}

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

                {{-- Barra compacta para filtrar sin ocupar gran parte de la pantalla. --}}
                <form class="card trace-filters" method="GET" action="{{ route('seguimiento.trazabilidad') }}">
                    <div class="trace-filters__top">
                        <div>
                            <div class="title">Filtros</div>
                            <div class="muted">Busca relaciones sin modificar información.</div>
                        </div>

                        <div class="trace-filters__legend" aria-label="Leyenda de instrumentos">
                            <span class="chip"><span class="dot blue"></span>ODS</span>
                            <span class="chip"><span class="dot green"></span>PND</span>
                            <span class="chip"><span class="dot orange"></span>OE</span>
                        </div>
                    </div>

                    <div class="trace-filters__fields">
                            <div>
                                <label class="label">Entidad</label>
                                <select class="input" name="entidad_id">
                                    <option value="">(Todas)</option>
                                    @foreach($entidades as $e)
                                        <option value="{{ $e->id }}" @selected((string)$fEntidad === (string)$e->id)>
                                            {{ $e->codigo ?? '' }}{{ isset($e->codigo) ? ' - ' : '' }}{{ $e->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">Meta</label>
                                <select class="input" name="meta_id">
                                    <option value="">(Todas)</option>
                                    @foreach($metas as $m)
                                        <option value="{{ $m->id }}" @selected((string)$fMeta === (string)$m->id)>
                                            {{ $m->codigo }} - {{ $m->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">ODS</label>
                                <select class="input" name="ods_id">
                                    <option value="">(Cualquiera)</option>
                                    @foreach($ods as $o)
                                        <option value="{{ $o->id }}" @selected((string)$fOds === (string)$o->id)>
                                            {{ $o->codigo }} - {{ $o->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">PND</label>
                                <select class="input" name="pdn_id">
                                    <option value="">(Cualquiera)</option>
                                    @foreach($pdns as $p)
                                        <option value="{{ $p->id }}" @selected((string)$fPdn === (string)$p->id)>
                                            {{ $p->codigo }} - {{ $p->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">Objetivo Estratégico</label>
                                <select class="input" name="objetivo_estrategico_id">
                                    <option value="">(Cualquiera)</option>
                                    @foreach($objetivos as $obj)
                                        <option value="{{ $obj->id }}" @selected((string)$fObjetivo === (string)$obj->id)>
                                            {{ $obj->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">Mostrar</label>
                                <select class="input" name="solo_activas">
                                    <option value="1" @selected((string)$fSoloActivas === '1')>Solo activas</option>
                                    <option value="0" @selected((string)$fSoloActivas === '0')>Todas</option>
                                </select>
                            </div>

                        <div class="trace-filters__actions">
                            <button class="btn" type="submit">Aplicar filtros</button>
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
                        @forelse($alineaciones as $a)
                            <div class="item">
                                <div class="row">
                                    <div>
                                        <div class="strong">
                                            {{ $a->meta->codigo ?? 'META' }} — {{ $a->meta->nombre ?? '-' }}
                                        </div>
                                        <div class="small" style="margin-top:4px;">
                                            Entidad: {{ $a->meta->plan->entidad->nombre ?? '—' }} ·
                                            Plan: {{ $a->meta->plan->codigo ?? '' }}
                                        </div>
                                    </div>

                                    <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                                        @if($a->ods_id)
                                            <span class="chip"><span class="dot blue"></span>ODS</span>
                                        @endif
                                        @if($a->pdn)
                                            <span class="chip"><span class="dot green"></span>PND</span>
                                        @endif
                                        @if($a->objetivo_estrategico_id)
                                            <span class="chip"><span class="dot orange"></span>OE</span>
                                        @endif

                                        <span class="chip">
                                            {{ $a->activo ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </div>
                                </div>

                                <div style="margin-top:10px; display:grid; gap:6px;">
                                    <div class="small"><span class="strong">ODS:</span> {{ $a->ods->codigo ?? '-' }} {{ $a->ods->nombre ?? '' }}</div>
                                    <div class="small"><span class="strong">PND:</span> {{ $a->pdn->codigo ?? '-' }} {{ $a->pdn->nombre ?? '' }}</div>
                                    <div class="small"><span class="strong">OE:</span> {{ $a->objetivoEstrategico->nombre ?? '-' }}</div>
                                </div>

                                <div style="margin-top:10px;">
                                    <a class="btn" href="{{ route('seguimiento.meta.show', $a->meta_id) }}">Ver meta →</a>
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
