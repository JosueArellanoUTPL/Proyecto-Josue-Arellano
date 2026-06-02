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

        // Si alguien llega sin login, lo mando al login.
        if (!$user) {
            return redirect()->route('login');
        }

        // Aqui se compara el rol del usuario contra los roles permitidos en la ruta.
        if (!in_array($user->role, $roles, true)) {
            abort(403, 'No autorizado.');
        }

        // Si el rol coincide, la peticion continua al controlador.
        return $next($request);
    }
}
