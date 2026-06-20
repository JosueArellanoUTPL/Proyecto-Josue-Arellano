<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // Mostrar historial de auditoria.
    public function index(Request $request)
    {
        // Consulta de auditoria.
        $query = AuditLog::with('user')->latest();

        // Filtros de auditoria.
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(15)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $modules = AuditLog::select('module')
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('auditoria.index', compact('logs', 'users', 'modules', 'actions'));
    }
}
