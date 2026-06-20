<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    // Registrar cambios realizados.
    public function handle(Request $request, Closure $next): Response
    {
        // Estado anterior del registro.
        $routeModel = $this->routeModel($request);
        $before = $this->modelSnapshot($routeModel);

        $response = $next($request);

        // Registro de auditoria.
        if ($this->shouldAudit($request, $response)) {
            AuditLog::create([
                'user_id' => $request->user()?->id,
                'module' => $this->moduleFromRoute($request),
                'action' => $this->actionFromRequest($request),
                'method' => $request->method(),
                'route_name' => $request->route()?->getName(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'description' => $this->description($request),
                'metadata' => [
                    'route_parameters' => $this->routeParameters($request),
                    'request_data' => $this->safeRequestData($request),
                    'before' => $before,
                    'after' => $routeModel?->exists ? $this->modelSnapshot($routeModel) : null,
                ],
            ]);
        }

        return $response;
    }

    // Decidir si se guarda la auditoria.
    private function shouldAudit(Request $request, Response $response): bool
    {
        // Validacion de auditoria.
        if (! $request->user()) {
            return false;
        }

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        return true;
    }

    // Obtener la accion realizada.
    private function actionFromRequest(Request $request): string
    {
        $method = $request->method();

        // Rutas que desactivan registros.
        $deactivationRoutes = [
            'entidades.destroy',
            'programas.destroy',
            'proyectos.destroy',
            'plans.destroy',
            'metas.destroy',
            'indicadores.destroy',
            'ods.destroy',
            'pdn.destroy',
            'objetivos-estrategicos.destroy',
            'alineaciones.destroy',
            'usuarios.destroy',
            'profile.destroy',
        ];

        if ($method === 'DELETE' && in_array($request->route()?->getName(), $deactivationRoutes, true)) {
            return 'desactivar';
        }

        return match ($method) {
            'POST' => 'crear',
            'PUT', 'PATCH' => 'actualizar',
            'DELETE' => 'eliminar',
            default => strtolower($method),
        };
    }

    // Obtener el modulo desde la ruta.
    private function moduleFromRoute(Request $request): string
    {
        $routeName = $request->route()?->getName();

        if (! $routeName) {
            return 'general';
        }

        return str($routeName)->before('.')->replace('-', ' ')->title()->toString();
    }

    // Crear descripcion de auditoria.
    private function description(Request $request): string
    {
        $routeName = $request->route()?->getName() ?? 'ruta sin nombre';

        return "Accion registrada desde la ruta {$routeName}.";
    }

    // Obtener parametros de la ruta.
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

    // Obtener modelo de la ruta.
    private function routeModel(Request $request): ?Model
    {
        return collect($request->route()?->parameters() ?? [])
            ->first(fn ($value) => $value instanceof Model);
    }

    // Copiar datos del modelo.
    private function modelSnapshot(?Model $model): ?array
    {
        if (! $model) {
            return null;
        }

        // Datos protegidos.
        return collect($model->getAttributes())
            ->except(['password', 'remember_token'])
            ->toArray();
    }

    // Quitar datos sensibles.
    private function safeRequestData(Request $request): array
    {
        $data = $request->except([
            '_token',
            '_method',
            'password',
            'password_confirmation',
            'evidencia',
            'evidencias',
        ]);

        return collect($data)->map(function ($value) {
            return is_scalar($value) || $value === null ? $value : '[dato compuesto]';
        })->toArray();
    }
}
