<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ExpedienteController;

use App\Http\Controllers\Admin\DependenciasDashboardController;
use App\Http\Controllers\Admin\InstitucionController;
use App\Http\Controllers\Admin\SubdependenciaController;


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

    // Habilitar / deshabilitar usuarios
    Route::patch('/admin/users/{user}/toggle', [UserManagementController::class, 'toggleStatus'])
        ->name('admin.users.toggle');

    // Vista de usuarios por dependencia
    Route::get('/admin/dependencias', [DependenciasDashboardController::class, 'index'])
        ->name('admin.dependencias.index');

    // CRUD Dependencias (instituciones)
    Route::resource('/admin/instituciones', InstitucionController::class)
        ->parameters(['instituciones' => 'institucion'])
        ->names('admin.instituciones');

    // CRUD Subdependencias
    Route::resource('/admin/subdependencias', SubdependenciaController::class)
        ->names('admin.subdependencias');

    // ordenar de arriba a abajo subdependencias
    Route::patch('/admin/subdependencias/{subdependencia}/up', [SubdependenciaController::class, 'moveUp'])
        ->name('admin.subdependencias.up');

    Route::patch('/admin/subdependencias/{subdependencia}/down', [SubdependenciaController::class, 'moveDown'])
        ->name('admin.subdependencias.down');


    //Asignar - quitar subdependencia a usuarios
    Route::post('/admin/dependencias/asignar', [DependenciasDashboardController::class, 'asignar'])
        ->name('admin.dependencias.asignar');

    Route::post('/admin/dependencias/quitar', [DependenciasDashboardController::class, 'quitar'])
        ->name('admin.dependencias.quitar');

    // Guardar Orden dependencias
    Route::patch('/admin/instituciones/{institucione}/orden', [InstitucionController::class, 'updateOrden'])
        ->name('admin.instituciones.orden');

    // orden dependencias
    Route::post('/admin/instituciones/orden/batch', [InstitucionController::class, 'updateOrdenBatch'])
        ->name('admin.instituciones.orden.batch');
    Route::post('/admin/subdependencias/orden/batch', [SubdependenciaController::class, 'updateOrdenBatch'])
      ->name('admin.subdependencias.orden.batch');

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
