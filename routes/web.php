<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ExpedienteController;


// Página principal
Route::get('/', function () {
    return view('welcome');
});

// Dashboard base (solo usuarios logueados)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas de PERFIL (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// RUTAS SOLO PARA ADMINISTRADOR
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // CRUD de usuarios
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    //Habilitar / deshabilitar usuarios
    Route::patch('/admin/users/{user}/toggle', [UserManagementController::class, 'toggleStatus'])->name('admin.users.toggle');
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

// Rutas para Expedientes
Route::middleware(['auth', 'role:capturista'])->group(function () {
    Route::resource('expedientes', ExpedienteController::class);
});


// === NUEVAS RUTAS FLUJO DE ESTADOS ===

// Rutas para Expedientes + flujo (CAPTURISTA)
Route::middleware(['auth', 'role:capturista'])->group(function () {

    // Solo las acciones que realmente usas
    Route::resource('expedientes', ExpedienteController::class)
        ->only(['index','create','store','edit','update','destroy']);

    // Enviar a validación / reenviar
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