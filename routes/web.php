<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EntidadController;

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

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 🔹 MÓDULO ENTIDADES (CRUD completo)
    Route::resource('entidades', EntidadController::class);

});

/*
|--------------------------------------------------------------------------
| Rutas por rol
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
| Rutas de autenticación (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
