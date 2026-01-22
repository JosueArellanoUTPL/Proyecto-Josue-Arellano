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
use App\Http\Controllers\ProyectoAvanceController;

// ✅ NUEVOS CONTROLLERS (seguimiento de Programa y Proyecto)
use App\Http\Controllers\SeguimientoProgramaController;
use App\Http\Controllers\SeguimientoProyectoController;

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

    // Dashboard (para cualquier usuario autenticado)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil (Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | SEGUIMIENTO (Admin y Técnico)
    |--------------------------------------------------------------------------
    */
    Route::get('/seguimiento/metas', [SeguimientoController::class, 'index'])->name('seguimiento.metas');
    Route::get('/seguimiento/metas/{meta}', [SeguimientoController::class, 'show'])->name('seguimiento.meta.show');

    Route::get('/seguimiento/indicadores/{indicador}/avance', [AvanceIndicadorController::class, 'create'])
        ->name('indicadores.avance.create');

    Route::post('/seguimiento/indicadores/{indicador}/avance', [AvanceIndicadorController::class, 'store'])
        ->name('indicadores.avance.store');

    Route::get('/seguimiento/organizacion', [OrganizacionController::class, 'index'])
        ->name('seguimiento.organizacion');

    Route::get('/seguimiento/organizacion/entidad/{entidad}', [OrganizacionController::class, 'show'])
        ->name('seguimiento.organizacion.entidad');

    // ✅ NUEVAS RUTAS: Seguimiento de Programas y Proyectos (para links clickeables)
    Route::get('/seguimiento/programas/{programa}', [SeguimientoProgramaController::class, 'show'])
        ->name('seguimiento.programa.show');

    Route::get('/seguimiento/proyectos/{proyecto}', [SeguimientoProyectoController::class, 'show'])
        ->name('seguimiento.proyecto.show');
        
    Route::get('/seguimiento/proyectos/{proyecto}/avances/create',
    [ProyectoAvanceController::class, 'create']
)->name('proyectos.avance.create');

Route::post('/seguimiento/proyectos/{proyecto}/avances',
    [ProyectoAvanceController::class, 'store']
)->name('proyectos.avance.store');

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRACIÓN (solo ADMIN)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {

        // Catálogos / Configuración
        Route::resource('entidades', EntidadController::class);
        Route::resource('programas', ProgramaController::class);
        Route::resource('proyectos', ProyectoController::class);

        Route::resource('objetivos-estrategicos', ObjetivoEstrategicoController::class);
        Route::resource('ods', OdsController::class);
        Route::resource('pdn', PdnController::class);

        // Planificación y Medición
        Route::resource('plans', PlanController::class);
        Route::resource('metas', MetaController::class);
        Route::resource('indicadores', IndicadorController::class);
        Route::resource('alineaciones', AlineacionController::class);

        // Seguridad
        Route::resource('usuarios', UserController::class)->except(['show']);
    });
});

/*
|--------------------------------------------------------------------------
| Rutas por rol (solo para pruebas / validación)
|--------------------------------------------------------------------------
*/
Route::get('/admin', function () {
    return 'Panel Admin OK';
})->middleware(['auth', 'role:admin']);

Route::get('/tecnico', function () {
    return 'Panel Técnico OK';
})->middleware(['auth', 'role:tecnico']);

/*
|--------------------------------------------------------------------------
| Rutas de autenticación (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
