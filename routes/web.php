<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ObjetivoEstrategicoController;
use App\Http\Controllers\OdsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PdnController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\IndicadorController;
use App\Http\Controllers\AlineacionController;
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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Perfil de usuario (Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Módulos del sistema (acceso general)
    |--------------------------------------------------------------------------
    */
    Route::resource('entidades', EntidadController::class);
    Route::resource('programas', ProgramaController::class);
    Route::resource('proyectos', ProyectoController::class);
    Route::resource('objetivos-estrategicos', ObjetivoEstrategicoController::class);
    Route::resource('ods', OdsController::class);
    Route::resource('pdn', PdnController::class);
    

    /*
    |--------------------------------------------------------------------------
    | Módulos solo para ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('usuarios', UserController::class)->except(['show']);
    });
    Route::middleware(['role:admin'])->group(function () {
    Route::resource('plans', PlanController::class);
    });
    Route::resource('metas', MetaController::class);
    Route::resource('indicadores', IndicadorController::class);
    Route::resource('alineaciones', AlineacionController::class);
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
