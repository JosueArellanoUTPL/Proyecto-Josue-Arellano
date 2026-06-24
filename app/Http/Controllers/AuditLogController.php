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
        $consulta = AuditLog::with('user')->latest();

        // Filtros de auditoria.
        if ($request->filled('user_id')) {
            $consulta->where('user_id', $request->user_id);
        }

        if ($request->filled('module')) {
            $consulta->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $consulta->where('action', $request->action);
        }

        if ($request->filled('from')) {
            $consulta->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $consulta->whereDate('created_at', '<=', $request->to);
        }

        $registrosAuditoria = $consulta->paginate(15)->withQueryString();

        $usuarios = User::orderBy('name')->get(['id', 'name', 'email']);
        $modulos = AuditLog::select('module')
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $acciones = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('auditoria.index', compact('registrosAuditoria', 'usuarios', 'modulos', 'acciones'));
    }
}
