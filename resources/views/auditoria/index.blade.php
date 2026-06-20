<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Auditoria del Sistema
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">
                {{-- Encabezado. --}}
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
                <form method="GET" action="{{ route('auditoria.index') }}" class="card" style="margin-top:16px;">
                    <div class="grid2">
                        <div>
                            <label class="label">Usuario</label>
                            <select name="user_id" class="input">
                                <option value="">Todos</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected((string)request('user_id') === (string)$user->id)>
                                        {{ $user->name }} - {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="label">Modulo</label>
                            <select name="module" class="input">
                                <option value="">Todos</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module }}" @selected(request('module') === $module)>
                                        {{ $module }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="label">Accion</label>
                            <select name="action" class="input">
                                <option value="">Todas</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" @selected(request('action') === $action)>
                                        {{ ucfirst($action) }}
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
                    </div>

                    <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="submit" class="btn">Filtrar</button>
                        <a href="{{ route('auditoria.index') }}" class="btn">Limpiar</a>
                    </div>
                </form>

                {{-- Historial. --}}
                <div class="card" style="margin-top:16px;">
                    <div class="title">Historial</div>
                    <div class="muted" style="margin-top:6px;">
                        Total mostrado: {{ $logs->count() }} de {{ $logs->total() }} registros.
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
                                @forelse($logs as $log)
                                    <tr class="border-b align-top">
                                        <td class="py-2 whitespace-nowrap">
                                            {{ $log->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="py-2">
                                            <strong>{{ $log->user->name ?? 'Usuario eliminado' }}</strong>
                                            <div class="muted">{{ $log->user->email ?? '' }}</div>
                                        </td>
                                        <td class="py-2">{{ $log->module ?? '-' }}</td>
                                        <td class="py-2">
                                            <span class="chip">{{ ucfirst($log->action) }}</span>
                                        </td>
                                        <td class="py-2">
                                            <div>{{ $log->route_name ?? '-' }}</div>
                                            <div class="muted">{{ $log->method }}</div>
                                        </td>
                                        <td class="py-2">{{ $log->ip_address ?? '-' }}</td>
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
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
