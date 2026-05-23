<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Middleware simple para control de acceso por rol.
     *
     * Ejemplos:
     * - ->middleware('role:admin')
     * - ->middleware('role:admin,planificacion')
     * - ->middleware('role:admin,tecnico')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Protección extra por si una ruta usa este middleware sin 'auth'.
        if (!$user) {
            return redirect()->route('login');
        }

        // Si el rol no está permitido, se bloquea la petición.
        if (!in_array($user->role, $roles, true)) {
            abort(403, 'No autorizado.');
        }

        return $next($request);
    }
}
