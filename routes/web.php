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
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | SEGUIMIENTO (Admin y Técnico)
    |--------------------------------------------------------------------------
    */

    // Seguimiento de metas
    Route::get('/seguimiento/metas', [SeguimientoController::class, 'index'])
        ->name('seguimiento.metas');

    Route::get('/seguimiento/metas/{meta}', [SeguimientoController::class, 'show'])
        ->name('seguimiento.meta.show');

    // Avances de indicadores (crear / guardar)
    Route::get('/seguimiento/indicadores/{indicador}/avance', [AvanceIndicadorController::class, 'create'])
        ->name('indicadores.avance.create');

    Route::post('/seguimiento/indicadores/{indicador}/avance', [AvanceIndicadorController::class, 'store'])
        ->name('indicadores.avance.store');

    // ✅ Avances de indicadores (editar / actualizar / eliminar)
    Route::get('/seguimiento/indicadores/avances/{avance}/edit', [AvanceIndicadorController::class, 'edit'])
        ->name('indicadores.avance.edit');

    Route::put('/seguimiento/indicadores/avances/{avance}', [AvanceIndicadorController::class, 'update'])
        ->name('indicadores.avance.update');

    Route::delete('/seguimiento/indicadores/avances/{avance}', [AvanceIndicadorController::class, 'destroy'])
        ->name('indicadores.avance.destroy');

    // Organización
    Route::get('/seguimiento/organizacion', [OrganizacionController::class, 'index'])
        ->name('seguimiento.organizacion');

    Route::get('/seguimiento/organizacion/entidad/{entidad}', [OrganizacionController::class, 'show'])
        ->name('seguimiento.organizacion.entidad');

    // Seguimiento por programa y proyecto
    Route::get('/seguimiento/programas/{programa}', [SeguimientoProgramaController::class, 'show'])
        ->name('seguimiento.programa.show');

    Route::get('/seguimiento/proyectos/{proyecto}', [SeguimientoProyectoController::class, 'show'])
        ->name('seguimiento.proyecto.show');

    // Matriz de trazabilidad institucional
    Route::get('/seguimiento/trazabilidad', [TrazabilidadController::class, 'index'])
        ->name('seguimiento.trazabilidad');

    /*
    |--------------------------------------------------------------------------
    | Avances de PROYECTOS
    |--------------------------------------------------------------------------
    */

    // Crear avance
    Route::get('/seguimiento/proyectos/{proyecto}/avances/create', [ProyectoAvanceController::class, 'create'])
        ->name('proyectos.avance.create');

    Route::post('/seguimiento/proyectos/{proyecto}/avances', [ProyectoAvanceController::class, 'store'])
        ->name('proyectos.avance.store');

    // Editar / actualizar avance
    Route::get('/seguimiento/proyectos/avances/{avance}/edit', [ProyectoAvanceController::class, 'edit'])
        ->name('proyectos.avance.edit');

    Route::put('/seguimiento/proyectos/avances/{avance}', [ProyectoAvanceController::class, 'update'])
        ->name('proyectos.avance.update');

    // Eliminar avance
    Route::delete('/seguimiento/proyectos/avances/{avance}', [ProyectoAvanceController::class, 'destroy'])
        ->name('proyectos.avance.destroy');

    /*
    |--------------------------------------------------------------------------
    | Evidencias de avances de PROYECTO (una por una)
    |--------------------------------------------------------------------------
    */

    // Agregar evidencia a un avance existente
    Route::post('/seguimiento/proyectos/avances/{avance}/evidencias', [ProyectoAvanceController::class, 'addEvidencia'])
        ->name('proyectos.avance.evidencia.add');

    // Eliminar evidencia individual
    Route::delete('/seguimiento/proyectos/evidencias/{evidencia}', [ProyectoAvanceController::class, 'deleteEvidencia'])
        ->name('proyectos.avance.evidencia.delete');

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRACIÓN (solo ADMIN)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {

        // Catálogos
        Route::resource('entidades', EntidadController::class);
        Route::resource('programas', ProgramaController::class);
        Route::resource('proyectos', ProyectoController::class);

        Route::resource('objetivos-estrategicos', ObjetivoEstrategicoController::class);
        Route::resource('ods', OdsController::class);
        Route::resource('pdn', PdnController::class);

        // Planificación
        Route::resource('plans', PlanController::class);
        Route::resource('metas', MetaController::class);
        Route::resource('indicadores', IndicadorController::class);

        // Alineaciones (se fuerza el nombre del parámetro para evitar "alineacione")
        Route::resource('alineaciones', AlineacionController::class)
            ->parameters(['alineaciones' => 'alineacion']);

        // Seguridad
        Route::resource('usuarios', UserController::class)->except(['show']);
    });
});

/*
|--------------------------------------------------------------------------
| Rutas por rol (pruebas)
|--------------------------------------------------------------------------
*/
Route::get('/admin', fn () => 'Panel Admin OK')
    ->middleware(['auth', 'role:admin']);

Route::get('/tecnico', fn () => 'Panel Técnico OK')
    ->middleware(['auth', 'role:tecnico']);

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
