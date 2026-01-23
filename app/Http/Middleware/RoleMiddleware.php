<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Middleware para control de acceso por rol.
     *
     * Este middleware se usa junto con las rutas:
     *   ->middleware('role:admin')
     *   ->middleware('role:tecnico')
     *
     * Permite que solo usuarios con uno de los roles indicados
     * puedan acceder a la ruta.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Obtenemos el usuario autenticado
        $user = $request->user();

        // 1️⃣ Si no hay usuario logueado
        // (normalmente no pasa porque ya usamos middleware 'auth',
        //  pero lo dejamos como protección extra)
        if (!$user) {
            return redirect()->route('login');
        }

        // 2️⃣ Si el rol del usuario NO está dentro de los roles permitidos
        // Ejemplo:
        // - Ruta con ->middleware('role:admin')
        // - Usuario con role = 'tecnico'
        // => acceso denegado (403)
        if (!in_array($user->role, $roles)) {
            abort(403, 'No autorizado.');
        }

        // 3️⃣ Si pasa todas las validaciones, continúa la petición
        return $next($request);
    }
}
