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
                            Visualiza cómo las metas (y opcionalmente indicadores) se alinean con ODS, PDN y Objetivos Estratégicos.
                        </div>
                    </div>
                    <a class="btn" href="{{ route('dashboard') }}">← Dashboard</a>
                </div>

                <div class="kpis">
                    <div class="kpi" style="background:#f0f4fb">
                        <div class="label">Total</div>
                        <div class="value">{{ $kpiTotal }}</div>
                    </div>
                    <div class="kpi" style="background:#eef7f5">
                        <div class="label">Con Indicador</div>
                        <div class="value">{{ $kpiConIndicador }}</div>
                    </div>
                    <div class="kpi">
                        <div class="label">Con ODS</div>
                        <div class="value">{{ $kpiConODS }}</div>
                    </div>
                    <div class="kpi">
                        <div class="label">Con PDN</div>
                        <div class="value">{{ $kpiConPDN }}</div>
                    </div>
                    <div class="kpi">
                        <div class="label">Con OE</div>
                        <div class="value">{{ $kpiConOE }}</div>
                    </div>
                </div>

                <div class="grid2">
                    <form class="card" method="GET" action="{{ route('seguimiento.trazabilidad') }}">
                        <div class="title">Filtros</div>
                        <div class="muted" style="margin-top:6px;">Se filtra la matriz sin modificar datos.</div>

                        <div style="display:grid; gap:12px; margin-top:12px;">
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
                                <label class="label">PDN</label>
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
                        </div>

                        <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
                            <button class="btn" type="submit">Aplicar filtros</button>
                            <a class="btn" href="{{ route('seguimiento.trazabilidad') }}">Limpiar</a>
                        </div>
                    </form>

                    <div class="card">
                        <div class="title">Leyenda</div>
                        <div class="muted" style="margin-top:6px;">
                            Cada registro representa una relación de trazabilidad (Meta/Indicador → Instrumentos).
                        </div>

                        <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
                            <span class="chip"><span class="dot blue"></span>ODS</span>
                            <span class="chip"><span class="dot green"></span>PDN</span>
                            <span class="chip"><span class="dot orange"></span>OE</span>
                        </div>

                        <div class="muted" style="margin-top:12px;">
                            El seguimiento operativo se observa en metas/indicadores y proyectos. Esta pantalla consolida el vínculo estratégico.
                        </div>
                    </div>
                </div>

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
                                        <div class="small" style="margin-top:4px;">
                                            Indicador: {{ $a->indicador->codigo ?? '-' }} {{ $a->indicador->nombre ?? '' }}
                                        </div>
                                    </div>

                                    <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                                        @if($a->ods_id)
                                            <span class="chip"><span class="dot blue"></span>ODS</span>
                                        @endif
                                        @if($a->pdn_id)
                                            <span class="chip"><span class="dot green"></span>PDN</span>
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
                                    <div class="small"><span class="strong">PDN:</span> {{ $a->pdn->codigo ?? '-' }} {{ $a->pdn->nombre ?? '' }}</div>
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
