<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    // Validar acceso segun el rol.
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Validacion de autenticacion.
        if (! $user) {
            return redirect()->route('login');
        }

        // Validacion de rol.
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'No autorizado.');
        }

        return $next($request);
    }
}
