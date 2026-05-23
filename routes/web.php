<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\EntidadController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\ProyectoController;

use App\Http\Controllers\ObjetivoEstrategicoController;
use App\Http\Controllers\OdsController;
use App\Http\Controllers\PdnController;

use App\Http\Controllers\PlanController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\IndicadorController;
use App\Http\Controllers\AlineacionController;

use App\Http\Controllers\UserController;

use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\AvanceIndicadorController;

use App\Http\Controllers\OrganizacionController;
use App\Http\Controllers\SeguimientoProgramaController;
use App\Http\Controllers\SeguimientoProyectoController;
use App\Http\Controllers\ProyectoAvanceController;

use App\Http\Controllers\TrazabilidadController;
use App\Http\Controllers\AuditLogController;
use App\Http\Middleware\AuditMiddleware;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas (login requerido)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', AuditMiddleware::class])->group(function () {

    // Dashboard: los 4 roles pueden consultar.
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Perfil propio del usuario autenticado.
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Seguimiento y consulta (los 4 roles)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:' . implode(',', User::roleKeys())])->group(function () {

        Route::get('/seguimiento/metas', [SeguimientoController::class, 'index'])
            ->name('seguimiento.metas');

        Route::get('/seguimiento/metas/{meta}', [SeguimientoController::class, 'show'])
            ->name('seguimiento.meta.show');

        Route::get('/seguimiento/organizacion', [OrganizacionController::class, 'index'])
            ->name('seguimiento.organizacion');

        Route::get('/seguimiento/organizacion/entidad/{entidad}', [OrganizacionController::class, 'show'])
            ->name('seguimiento.organizacion.entidad');

        Route::get('/seguimiento/programas/{programa}', [SeguimientoProgramaController::class, 'show'])
            ->name('seguimiento.programa.show');

        Route::get('/seguimiento/proyectos/{proyecto}', [SeguimientoProyectoController::class, 'show'])
            ->name('seguimiento.proyecto.show');

        Route::get('/seguimiento/trazabilidad', [TrazabilidadController::class, 'index'])
            ->name('seguimiento.trazabilidad');
    });

    /*
    |--------------------------------------------------------------------------
    | Registro de avances y evidencias (Admin y Técnico de Seguimiento)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,tecnico'])->group(function () {

        // Avances de indicadores.
        Route::get('/seguimiento/indicadores/{indicador}/avance', [AvanceIndicadorController::class, 'create'])
            ->name('indicadores.avance.create');

        Route::post('/seguimiento/indicadores/{indicador}/avance', [AvanceIndicadorController::class, 'store'])
            ->name('indicadores.avance.store');

        Route::get('/seguimiento/indicadores/avances/{avance}/edit', [AvanceIndicadorController::class, 'edit'])
            ->name('indicadores.avance.edit');

        Route::put('/seguimiento/indicadores/avances/{avance}', [AvanceIndicadorController::class, 'update'])
            ->name('indicadores.avance.update');

        Route::delete('/seguimiento/indicadores/avances/{avance}', [AvanceIndicadorController::class, 'destroy'])
            ->name('indicadores.avance.destroy');

        // Avances de proyectos.
        Route::get('/seguimiento/proyectos/{proyecto}/avances/create', [ProyectoAvanceController::class, 'create'])
            ->name('proyectos.avance.create');

        Route::post('/seguimiento/proyectos/{proyecto}/avances', [ProyectoAvanceController::class, 'store'])
            ->name('proyectos.avance.store');

        Route::get('/seguimiento/proyectos/avances/{avance}/edit', [ProyectoAvanceController::class, 'edit'])
            ->name('proyectos.avance.edit');

        Route::put('/seguimiento/proyectos/avances/{avance}', [ProyectoAvanceController::class, 'update'])
            ->name('proyectos.avance.update');

        Route::delete('/seguimiento/proyectos/avances/{avance}', [ProyectoAvanceController::class, 'destroy'])
            ->name('proyectos.avance.destroy');

        // Evidencias de avances de proyecto.
        Route::post('/seguimiento/proyectos/avances/{avance}/evidencias', [ProyectoAvanceController::class, 'addEvidencia'])
            ->name('proyectos.avance.evidencia.add');

        Route::delete('/seguimiento/proyectos/evidencias/{evidencia}', [ProyectoAvanceController::class, 'deleteEvidencia'])
            ->name('proyectos.avance.evidencia.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | Planificación (Admin y Responsable de Planificación)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,planificacion'])->group(function () {

        Route::resource('entidades', EntidadController::class);
        Route::resource('programas', ProgramaController::class);
        Route::resource('proyectos', ProyectoController::class);

        Route::resource('objetivos-estrategicos', ObjetivoEstrategicoController::class);
        Route::resource('ods', OdsController::class);
        Route::resource('pdn', PdnController::class);

        Route::resource('plans', PlanController::class);
        Route::resource('metas', MetaController::class);
        Route::resource('indicadores', IndicadorController::class);

        Route::resource('alineaciones', AlineacionController::class)
            ->parameters(['alineaciones' => 'alineacion']);
    });

    /*
    |--------------------------------------------------------------------------
    | Administración y seguridad (solo Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {

        Route::resource('usuarios', UserController::class)->except(['show']);

        Route::get('/auditoria', [AuditLogController::class, 'index'])
            ->name('auditoria.index');
    });
});

/*
|--------------------------------------------------------------------------
| Rutas por rol (pruebas simples)
|--------------------------------------------------------------------------
*/
Route::get('/admin', fn () => 'Panel Admin OK')
    ->middleware(['auth', 'role:admin']);

Route::get('/tecnico', fn () => 'Panel Técnico OK')
    ->middleware(['auth', 'role:tecnico']);

Route::get('/consulta', fn () => 'Panel Consulta OK')
    ->middleware(['auth', 'role:consulta']);

Route::get('/planificacion', fn () => 'Panel Planificación OK')
    ->middleware(['auth', 'role:planificacion']);

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
