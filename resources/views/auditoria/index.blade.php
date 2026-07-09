<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Auditoria del Sistema
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">
                <div class="row">
                    <div>
                        <div class="title">Registro de acciones</div>
                
                        <div class="muted" style="margin-top:6px;">
                            Consulta quien realizo cambios, en que modulo y desde que ruta.
                        </div>
                    </div>

                    <a class="btn" href="{{ route('dashboard') }}">Volver al Dashboard</a>
                </div>

                {{-- Filtros. --}}
                <form method="GET" action="{{ route('auditoria.index') }}" class="card trace-filters audit-filters">
                    <div class="trace-filters__fields audit-filters__fields">
                        <div>
                            <label class="label">Usuario</label>
                            <select name="user_id" class="input">
                                <option value="">Todos</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" @selected((string)request('user_id') === (string)$usuario->id)>
                                        {{ $usuario->name }} - {{ $usuario->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="label">Modulo</label>
                            <select name="module" class="input">
                                <option value="">Todos</option>
                                @foreach($modulos as $modulo)
                                    <option value="{{ $modulo }}" @selected(request('module') === $modulo)>
                                        {{ $modulo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="label">Accion</label>
                            <select name="action" class="input">
                                <option value="">Todas</option>
                                @foreach($acciones as $accion)
                                    <option value="{{ $accion }}" @selected(request('action') === $accion)>
                                        {{ ucfirst($accion) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="label">Desde</label>
                            <input type="date" name="from" class="input" value="{{ request('from') }}">
                        </div>

                        <div>
                            <label class="label">Hasta</label>
                            <input type="date" name="to" class="input" value="{{ request('to') }}">
                        </div>
                        {{-- botones de filtros --}}
                        <div class="trace-filters__actions audit-filters__actions">
                            <button type="submit" class="btn">Filtrar</button>
                            <a href="{{ route('auditoria.index') }}" class="btn">Limpiar</a>
                        </div>
                    </div>
                </form>

                {{-- Historial de auditoria. --}}
                <div class="card" style="margin-top:16px;">
                    <div class="title">Historial</div>

                    <div class="muted" style="margin-top:6px;">
                        Total mostrado: {{ $registrosAuditoria->count() }} de {{ $registrosAuditoria->total() }} registros.
                    </div>

                    <div class="overflow-x-auto" style="margin-top:14px;">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2">Fecha</th>
                                    <th class="py-2">Usuario</th>
                                    <th class="py-2">Modulo</th>
                                    <th class="py-2">Accion</th>
                                    <th class="py-2">Ruta</th>
                                    <th class="py-2">IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($registrosAuditoria as $registro)
                                    <tr class="border-b align-top">
                                        {{-- fecha --}}
                                        <td class="py-2 whitespace-nowrap">
                                            {{ $registro->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        {{-- usuario --}}
                                        <td class="py-2">
                                            <strong>{{ $registro->user->name ?? 'Usuario eliminado' }}</strong>
                                            <div class="muted">{{ $registro->user->email ?? '' }}</div>
                                        </td>
                                        {{-- modulo --}}
                                        <td class="py-2">{{ $registro->module ?? '-' }}</td>
                                        {{-- accion --}}
                                        <td class="py-2">
                                            <span class="chip">{{ ucfirst($registro->action) }}</span>
                                        </td>
                                        {{-- ruta --}}
                                        <td class="py-2">
                                            <div>{{ $registro->route_name ?? '-' }}</div>
                                            <div class="muted">{{ $registro->method }}</div>
                                        </td>
                                        {{-- ip --}}
                                        <td class="py-2">{{ $registro->ip_address ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-center muted">
                                            No hay registros de auditoria con los filtros actuales.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top:16px;">
                        {{ $registrosAuditoria->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
