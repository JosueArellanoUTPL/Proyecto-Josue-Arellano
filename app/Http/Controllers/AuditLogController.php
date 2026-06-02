<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // Consulta principal de auditoria: trae los registros mas recientes.
        $query = AuditLog::with('user')->latest();

        // Filtros de la pantalla de auditoria.
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

        // Paginacion de 15 para que la tabla no crezca demasiado.
        $logs = $query->paginate(15)->withQueryString();

        // Datos para llenar los select de filtros.
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
