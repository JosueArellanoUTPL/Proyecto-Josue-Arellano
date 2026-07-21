<?php

use App\Http\Controllers\AlineacionController;
use App\Http\Controllers\ActividadOperativaController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AvanceIndicadorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\IndicadorController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\ObjetivoEstrategicoController;
use App\Http\Controllers\OdsController;
use App\Http\Controllers\OrganizacionController;
use App\Http\Controllers\PdnController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\ProyectoAvanceController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\SeguimientoProgramaController;
use App\Http\Controllers\SeguimientoProyectoController;
use App\Http\Controllers\TrazabilidadController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuditMiddleware;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Rutas publicas.
Route::get('/', function () {
    return view('welcome');
});

// Rutas con autenticación y auditoría.
Route::middleware(['auth', AuditMiddleware::class])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // Consulta para los cuatro roles.
    Route::middleware(['role:'.implode(',', User::roleKeys())])->group(function () {

        Route::get('/seguimiento/metas', [SeguimientoController::class, 'index'])
            ->name('seguimiento.metas');

        Route::get('/seguimiento/metas/{meta}', [SeguimientoController::class, 'show'])
            ->name('seguimiento.meta.show');

        Route::get('/seguimiento/poa', [SeguimientoController::class, 'poa'])
            ->name('seguimiento.poa');

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

        Route::get('/reportes', [ReporteController::class, 'index'])
            ->name('reportes.index');

        Route::get('/reportes/metas', [ReporteController::class, 'metas'])
            ->name('reportes.metas');

        Route::get('/reportes/proyectos', [ReporteController::class, 'proyectos'])
            ->name('reportes.proyectos');

        Route::get('/reportes/trazabilidad', [ReporteController::class, 'trazabilidad'])
            ->name('reportes.trazabilidad');

        Route::get('/reportes/poa', [ReporteController::class, 'poa'])
            ->name('reportes.poa');

        Route::get('/reportes/poa/csv', [ReporteController::class, 'poaCsv'])
            ->name('reportes.poa.csv');
    });

    // Seguimiento para administrador y tecnico.
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

        // Evidencias de proyectos.
        Route::post('/seguimiento/proyectos/avances/{avance}/evidencias', [ProyectoAvanceController::class, 'addEvidencia'])
            ->name('proyectos.avance.evidencia.add');

        Route::delete('/seguimiento/proyectos/evidencias/{evidencia}', [ProyectoAvanceController::class, 'deleteEvidencia'])
            ->name('proyectos.avance.evidencia.delete');
    });

    // CRUD de planificacion.
    Route::middleware(['role:admin,planificacion'])->group(function () {

        Route::resource('entidades', EntidadController::class)
            ->except(['show'])
            ->parameters(['entidades' => 'entidad']);
        Route::resource('programas', ProgramaController::class)->except(['show']);
        Route::resource('proyectos', ProyectoController::class)->except(['show']);

        Route::resource('objetivos-estrategicos', ObjetivoEstrategicoController::class)
            ->except(['show'])
            ->parameters(['objetivos-estrategicos' => 'objetivo']);
        Route::resource('ods', OdsController::class)
            ->except(['show'])
            ->parameters(['ods' => 'ods']);
        Route::resource('pdn', PdnController::class)->except(['show']);

        Route::resource('planes', PlanController::class)
            ->except(['show'])
            ->parameters(['planes' => 'plan']);
        Route::resource('metas', MetaController::class)->except(['show']);
        Route::resource('indicadores', IndicadorController::class)
            ->except(['show'])
            ->parameters(['indicadores' => 'indicador']);

        Route::resource('alineaciones', AlineacionController::class)
            ->except(['show'])
            ->parameters(['alineaciones' => 'alineacion']);
    });

    // Flujo POA: consulta para planificador, aprobador y administrador.
    Route::middleware(['role:admin,planificacion,aprobador'])->group(function () {
        Route::get('actividades-operativas', [ActividadOperativaController::class, 'index'])
            ->name('actividades-operativas.index');
    });

    // Registro y envío a revisión por planificación.
    Route::middleware(['role:admin,planificacion'])->group(function () {
        Route::get('actividades-operativas/create', [ActividadOperativaController::class, 'create'])->name('actividades-operativas.create');
        Route::post('actividades-operativas', [ActividadOperativaController::class, 'store'])->name('actividades-operativas.store');
        Route::get('actividades-operativas/{actividadOperativa}/edit', [ActividadOperativaController::class, 'edit'])->name('actividades-operativas.edit');
        Route::put('actividades-operativas/{actividadOperativa}', [ActividadOperativaController::class, 'update'])->name('actividades-operativas.update');
        Route::delete('actividades-operativas/{actividadOperativa}', [ActividadOperativaController::class, 'destroy'])->name('actividades-operativas.destroy');
        Route::post('actividades-operativas/{actividadOperativa}/enviar-revision', [ActividadOperativaController::class, 'enviarRevision'])->name('actividades-operativas.enviar-revision');
        Route::post('actividades-operativas/{actividadOperativa}/cambiar-estado', [ActividadOperativaController::class, 'cambiarEstado'])->name('actividades-operativas.cambiar-estado');
    });

    // Revisión y decisión de aprobación.
    Route::middleware(['role:admin,aprobador'])->group(function () {
        Route::get('actividades-operativas/{actividadOperativa}/revision', [ActividadOperativaController::class, 'revisar'])->name('actividades-operativas.revisar');
        Route::post('actividades-operativas/{actividadOperativa}/decision', [ActividadOperativaController::class, 'decidir'])->name('actividades-operativas.decision');
    });

    // Administracion y auditoria.
    Route::middleware(['role:admin'])->group(function () {

        Route::resource('usuarios', UserController::class)
            ->except(['show'])
            ->parameters(['usuarios' => 'usuario']);

        Route::get('/auditoria', [AuditLogController::class, 'index'])
            ->name('auditoria.index');
    });
});

// Rutas de autenticación.
require __DIR__.'/auth.php';
