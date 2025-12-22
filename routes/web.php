<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ExpedienteController;

use App\Http\Controllers\Admin\DependenciasDashboardController;
use App\Http\Controllers\Admin\InstitucionController;
use App\Http\Controllers\Admin\SubdependenciaController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaspCatalogoController;

// Página principal
Route::get('/', function () {
    return view('welcome');
});

// Dashboard base (redirigir por rol)
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->role?->nombre === 'admin') return redirect()->route('admin.dashboard');
    if ($user?->role?->nombre === 'capturista') return redirect()->route('capturista.dashboard');
    if ($user?->role?->nombre === 'validador') return redirect()->route('validador.dashboard');
    if ($user?->role?->nombre === 'lector') return redirect()->route('lector.dashboard');

    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Rutas de PERFIL (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// RUTAS SOLO PARA ADMINISTRADOR
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // CRUD de Usuarios
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        // Habilitar / deshabilitar usuarios
        Route::patch('/users/{user}/toggle', [UserManagementController::class, 'toggleStatus'])->name('users.toggle');
        // Vista de usuarios por dependencia
        Route::get('/dependencias', [DependenciasDashboardController::class, 'index'])->name('dependencias.index');
        // CRUD Dependencias (instituciones)
        Route::resource('/instituciones', InstitucionController::class)
            ->parameters(['instituciones' => 'institucion'])
            ->names('instituciones');
        // CRUD Subdependencias
        Route::resource('/subdependencias', SubdependenciaController::class)
            ->names('subdependencias');
        // ordenar de arriba a abajo subdependencias
        Route::patch('/subdependencias/{subdependencia}/up', [SubdependenciaController::class, 'moveUp'])->name('subdependencias.up');
        Route::patch('/subdependencias/{subdependencia}/down', [SubdependenciaController::class, 'moveDown'])->name('subdependencias.down');
        //Asignar - quitar dependencia a usuarios
        Route::post('/dependencias/asignar', [DependenciasDashboardController::class, 'asignar'])->name('dependencias.asignar');
        Route::post('/dependencias/quitar', [DependenciasDashboardController::class, 'quitar'])->name('dependencias.quitar');
        // orden dependencias / Subdependencias
        Route::patch('/instituciones/{institucione}/orden', [InstitucionController::class, 'updateOrden'])->name('instituciones.orden');
        Route::post('/instituciones/orden/batch', [InstitucionController::class, 'updateOrdenBatch'])->name('instituciones.orden.batch');
        Route::post('/subdependencias/orden/batch', [SubdependenciaController::class, 'updateOrdenBatch'])->name('subdependencias.orden.batch');

        // CATALOGO EXCEL
        Route::get('/catalogo-fasp', [FaspCatalogoController::class, 'index'])
        ->name('fasp.index');

        Route::post('/catalogo-fasp/import', [FaspCatalogoController::class, 'import'])
        ->name('fasp.import');

        Route::delete('/catalogo-fasp', [FaspCatalogoController::class, 'destroyByYear'])
        ->name('fasp.destroy');

        Route::patch('/catalogo-fasp/{row}', [FaspCatalogoController::class, 'update'])
        ->name('fasp.update');

        Route::post('/catalogo-fasp/recalcular', [FaspCatalogoController::class, 'recalcular'])
        ->name('fasp.recalcular');
    });

// RUTAS SOLO PARA DEPENDENCIAS
Route::middleware(['auth', 'role:dependencia'])->group(function () {
    Route::get('/dependencia/dashboard', function () {
        return "Panel de Dependencias";
    })->name('dependencia.dashboard');
});

// Rutas para CAPTURISTA
Route::middleware(['auth', 'role:capturista'])->group(function () {
    Route::get('/capturista/dashboard', function () {
        return view('capturista.dashboard');
    })->name('capturista.dashboard');
});

// Rutas para VALIDADOR
Route::middleware(['auth', 'role:validador'])->group(function () {
    Route::get('/validador/dashboard', function () {
        return view('validador.dashboard');
    })->name('validador.dashboard');
});

// Rutas para LECTOR
Route::middleware(['auth', 'role:lector'])->group(function () {
    Route::get('/lector/dashboard', function () {
        return "Panel del Lector";
    })->name('lector.dashboard');
});

// === NUEVAS RUTAS FLUJO DE ESTADOS ===

// Rutas para Expedientes + flujo (CAPTURISTA)
Route::middleware(['auth', 'role:capturista'])->group(function () {

    Route::resource('expedientes', ExpedienteController::class)
        ->only(['index','create','store','edit','update','destroy']);

    Route::post('/expedientes/{expediente}/enviar-validacion', [ExpedienteController::class, 'enviarValidacion'])
        ->name('expedientes.enviar-validacion');

    Route::post('/expedientes/{expediente}/reenviar-validacion', [ExpedienteController::class, 'reenviarValidacion'])
        ->name('expedientes.reenviar-validacion');
});

// Validador (revisor): listar / ver / decidir
Route::middleware(['auth', 'role:validador'])
    ->prefix('validador')
    ->name('validador.')
    ->group(function () {

        Route::get('/expedientes', [ExpedienteController::class, 'listaEnValidacion'])
            ->name('expedientes.index');

        Route::get('/expedientes/{expediente}', [ExpedienteController::class, 'mostrarParaRevision'])
            ->name('expedientes.show');

        Route::post('/expedientes/{expediente}/decidir', [ExpedienteController::class, 'decidir'])
            ->name('expedientes.decidir');
    });

// Auth Breeze
require __DIR__.'/auth.php';
