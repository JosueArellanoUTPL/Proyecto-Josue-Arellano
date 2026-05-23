<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Registra acciones de escritura realizadas por usuarios autenticados.
     *
     * No registra consultas GET porque eso llenaría la tabla demasiado rápido.
     * Para este proyecto nos interesa auditar cambios: crear, actualizar y eliminar.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

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
        if (!$request->user()) {
            return false;
        }

        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        return true;
    }

    private function actionFromMethod(string $method): string
    {
        return match ($method) {
            'POST' => 'crear',
            'PUT', 'PATCH' => 'actualizar',
            'DELETE' => 'eliminar',
            default => strtolower($method),
        };
    }

    private function moduleFromRoute(Request $request): string
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return 'general';
        }

        return str($routeName)->before('.')->replace('-', ' ')->title()->toString();
    }

    private function description(Request $request): string
    {
        $routeName = $request->route()?->getName() ?? 'ruta sin nombre';

        return "Acción registrada desde la ruta {$routeName}.";
    }

    private function routeParameters(Request $request): array
    {
        $parameters = $request->route()?->parameters() ?? [];

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
