<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Primero deja que Laravel ejecute la ruta normal.
        $response = $next($request);

        // Despues de guardar/editar/eliminar, aqui se registra la accion.
        if ($this->shouldAudit($request, $response)) {
            AuditLog::create([
                'user_id' => $request->user()?->id,
                'module' => $this->moduleFromRoute($request),
                'action' => $this->actionFromMethod($request->method()),
                'method' => $request->method(),
                'route_name' => $request->route()?->getName(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'description' => $this->description($request),
                'metadata' => [
                    'route_parameters' => $this->routeParameters($request),
                ],
            ]);
        }

        return $response;
    }

    private function shouldAudit(Request $request, Response $response): bool
    {
        // Solo audito usuarios logueados.
        if (!$request->user()) {
            return false;
        }

        // No audito GET porque solo consulta y llenaria mucho la tabla.
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        // Si la accion fallo, no la guardo como cambio exitoso.
        if ($response->getStatusCode() >= 400) {
            return false;
        }

        return true;
    }

    private function actionFromMethod(string $method): string
    {
        // Traduce el metodo HTTP a una palabra sencilla para la tabla.
        return match ($method) {
            'POST' => 'crear',
            'PUT', 'PATCH' => 'actualizar',
            'DELETE' => 'eliminar',
            default => strtolower($method),
        };
    }

    private function moduleFromRoute(Request $request): string
    {
        // El modulo sale del nombre de la ruta: metas.index => Metas.
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return 'general';
        }

        return str($routeName)->before('.')->replace('-', ' ')->title()->toString();
    }

    private function description(Request $request): string
    {
        $routeName = $request->route()?->getName() ?? 'ruta sin nombre';

        return "Accion registrada desde la ruta {$routeName}.";
    }

    private function routeParameters(Request $request): array
    {
        $parameters = $request->route()?->parameters() ?? [];

        // Guarda datos basicos del modelo usado en la ruta, por ejemplo Meta id 3.
        return collect($parameters)->map(function ($value) {
            if (is_object($value) && method_exists($value, 'getKey')) {
                return [
                    'model' => class_basename($value),
                    'id' => $value->getKey(),
                ];
            }

            return $value;
        })->toArray();
    }
}
