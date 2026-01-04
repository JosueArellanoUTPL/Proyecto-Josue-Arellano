<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Si no está logueado
        if (!$user) {
            return redirect()->route('login');
        }

        // Si no tiene rol permitido
        if (!in_array($user->role, $roles)) {
            abort(403, 'No autorizado.');
        }

        return $next($request);
    }
}
