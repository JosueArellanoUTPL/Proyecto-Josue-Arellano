<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Guarda una copia sencilla del modelo antes de actualizarlo.
        $routeModel = $this->routeModel($request);
        $before = $this->modelSnapshot($routeModel);

        // Primero deja que Laravel ejecute la ruta normal.
        $response = $next($request);

        // Despues de guardar/editar/eliminar, aqui se registra la accion.
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

    private function actionFromRequest(Request $request): string
    {
        $method = $request->method();

        // Estos DELETE conservan el registro y solamente cambian activo a false.
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

    private function routeModel(Request $request): ?Model
    {
        // Toma el primer modelo recibido por la ruta, por ejemplo Proyecto o Meta.
        return collect($request->route()?->parameters() ?? [])
            ->first(fn ($value) => $value instanceof Model);
    }

    private function modelSnapshot(?Model $model): ?array
    {
        if (!$model) {
            return null;
        }

        // Nunca se guardan claves ni tokens dentro de la auditoría.
        return collect($model->getAttributes())
            ->except(['password', 'remember_token'])
            ->toArray();
    }

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

        // La bitácora guarda valores simples y evita serializar objetos o archivos.
        return collect($data)->map(function ($value) {
            return is_scalar($value) || $value === null ? $value : '[dato compuesto]';
        })->toArray();
    }
}
