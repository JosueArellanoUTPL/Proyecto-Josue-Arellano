<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * Este archivo es el punto de arranque de Laravel 12.
 * Aquí se configuran:
 * - rutas principales (web, console)
 * - middlewares globales y alias
 * - manejo de excepciones
 */
return Application::configure(basePath: dirname(__DIR__))

    // 1) Configuración de rutas
    // - web.php: rutas con Blade, sesiones, auth, etc.
    // - console.php: comandos artisan personalizados
    // - health: endpoint de salud "/up"
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    // 2) Middlewares
    // Aquí registramos alias para poder usar en rutas:
    // ->middleware('role:admin')
    // ->middleware('role:tecnico')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })

    // 3) Manejo de excepciones
    // (Por ahora lo dejamos vacío, Laravel maneja lo base automáticamente)
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    // 4) Crear la instancia de la aplicación
    ->create();
