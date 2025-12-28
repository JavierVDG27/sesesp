<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DependenciasDashboardController;
use App\Http\Controllers\Admin\InstitucionController;
use App\Http\Controllers\Admin\SubdependenciaController;
use App\Http\Controllers\Admin\FaspCatalogoController;

// Expedientes
use App\Http\Controllers\ExpedienteController;

// Validador - Asignaciones por institución
use App\Http\Controllers\Validador\FaspAsignacionesInstitucionController;
use App\Http\Controllers\Validador\FaspDistribucionesController;

// Capturista
use App\Models\FaspAsignacionInstitucion;

// Página principal
Route::get('/', function () {
    return view('welcome');
});

// Dashboard base (redirigir por rol)
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->role?->name === 'admin') return redirect()->route('admin.dashboard');
    if ($user?->role?->name === 'capturista') return redirect()->route('capturista.dashboard');
    if ($user?->role?->name === 'validador') return redirect()->route('validador.dashboard');
    if ($user?->role?->name === 'lector') return redirect()->route('lector.dashboard');

    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Rutas de PERFIL (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =========================
// ADMIN
// =========================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // CRUD de Usuarios
        Route::resource('/users', UserManagementController::class)
            ->except(['show'])
            ->names('users');

        // Habilitar / deshabilitar usuarios
        Route::patch('/users/{user}/toggle', [UserManagementController::class, 'toggleStatus'])->name('users.toggle');

        // Vista de usuarios por dependencia
        Route::get('/dependencias', [DependenciasDashboardController::class, 'index'])->name('dependencias.index');

        // Asignar - quitar dependencia a usuarios
        Route::post('/dependencias/asignar', [DependenciasDashboardController::class, 'asignar'])->name('dependencias.asignar');
        Route::post('/dependencias/quitar', [DependenciasDashboardController::class, 'quitar'])->name('dependencias.quitar');

        // CRUD Instituciones
        Route::resource('/instituciones', InstitucionController::class)
            ->parameters(['instituciones' => 'institucion'])
            ->names('instituciones');

        // Orden Instituciones
        Route::patch('/instituciones/{institucion}/orden', [InstitucionController::class, 'updateOrden'])->name('instituciones.orden');
        Route::post('/instituciones/orden/batch', [InstitucionController::class, 'updateOrdenBatch'])->name('instituciones.orden.batch');

        // CRUD Subdependencias
        Route::resource('/subdependencias', SubdependenciaController::class)
            ->names('subdependencias');

        // Orden Subdependencias (flechas)
        Route::patch('/subdependencias/{subdependencia}/up', [SubdependenciaController::class, 'moveUp'])->name('subdependencias.up');
        Route::patch('/subdependencias/{subdependencia}/down', [SubdependenciaController::class, 'moveDown'])->name('subdependencias.down');

        // Orden Subdependencias batch
        Route::post('/subdependencias/orden/batch', [SubdependenciaController::class, 'updateOrdenBatch'])->name('subdependencias.orden.batch');

        // Catálogo FASP (Excel)
        Route::get('/catalogo-fasp', [FaspCatalogoController::class, 'index'])->name('fasp.index');
        Route::post('/catalogo-fasp/import', [FaspCatalogoController::class, 'import'])->name('fasp.import');
        Route::post('/catalogo-fasp', [FaspCatalogoController::class, 'store'])->name('fasp.store');
        Route::delete('/catalogo-fasp', [FaspCatalogoController::class, 'destroyByYear'])->name('fasp.destroyByYear');
        Route::delete('/catalogo-fasp/{row}', [FaspCatalogoController::class, 'destroy'])->name('fasp.destroyRow');
        Route::patch('/catalogo-fasp/{row}', [FaspCatalogoController::class, 'update'])->name('fasp.update');
        Route::post('/catalogo-fasp/recalcular', [FaspCatalogoController::class, 'recalcular'])->name('fasp.recalcular');
    });

// =========================
// CAPTURISTA
// =========================
    Route::middleware(['auth', 'role:capturista'])->group(function () {

        Route::get('/capturista/dashboard', function () {
            $user = auth()->user();
            $institucionId = $user?->institucion_id;

            $asignacionesCount = 0;

            if ($institucionId) {
                $asignacionesCount = FaspAsignacionInstitucion::query()
                    ->activas()
                    ->where('institucion_id', $institucionId)
                    ->where('nivel', 3)
                    ->count();
            }

            return view('capturista.dashboard', compact('asignacionesCount'));
        })->name('capturista.dashboard');

        // ✅ Expedientes (capturista)
        Route::resource('expedientes', ExpedienteController::class)
            ->only(['index','create','store','edit','update','destroy']);

        Route::post('/expedientes/{expediente}/enviar-validacion', [ExpedienteController::class, 'enviarValidacion'])
            ->name('expedientes.enviar-validacion');

        Route::post('/expedientes/{expediente}/reenviar-validacion', [ExpedienteController::class, 'reenviarValidacion'])
            ->name('expedientes.reenviar-validacion');
    });

// =========================
// VALIDADOR
// =========================
Route::middleware(['auth', 'role:validador'])
    ->prefix('validador')
    ->name('validador.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('validador.dashboard');
        })->name('dashboard');

        // Bandeja / revisión de expedientes
        Route::get('/expedientes', [ExpedienteController::class, 'listaEnValidacion'])->name('expedientes.index');
        Route::get('/expedientes/{expediente}', [ExpedienteController::class, 'mostrarParaRevision'])->name('expedientes.show');
        Route::post('/expedientes/{expediente}/decidir', [ExpedienteController::class, 'decidir'])->name('expedientes.decidir');

        // Asignaciones FASP por institución (catálogo solo lectura + asignar/quitar)
        Route::get('/fasp-asignaciones', [FaspAsignacionesInstitucionController::class, 'index'])
            ->name('fasp_asignaciones_institucion.index');

        Route::post('/fasp-asignaciones/asignar', [FaspAsignacionesInstitucionController::class, 'asignar'])
            ->name('fasp_asignaciones_institucion.asignar');

        Route::delete('/fasp-asignaciones/{asignacion}', [FaspAsignacionesInstitucionController::class, 'quitar'])
            ->name('fasp_asignaciones_institucion.quitar');

        // Ver/editar distribución de un subprograma (por catálogo row)
        Route::get('/fasp-distribuciones/{row}/edit', [FaspDistribucionesController::class, 'edit'])
            ->name('fasp_distribuciones.edit');

        // Asignar distribución de montos
        Route::post('/fasp-distribuciones', [FaspDistribucionesController::class, 'store'])
        ->name('fasp_distribuciones.store');
        Route::delete('/fasp-distribuciones/{dist}', [FaspDistribucionesController::class, 'destroy'])
        ->name('fasp_distribuciones.destroy');
        Route::post('/fasp-distribuciones/lock', [FaspDistribucionesController::class, 'lock'])
        ->name('fasp_distribuciones.lock');
        Route::post('/fasp-distribuciones/unlock', [FaspDistribucionesController::class, 'unlock'])
        ->name('fasp_distribuciones.unlock');
    });

// =========================
// LECTOR
// =========================
Route::middleware(['auth', 'role:lector'])->group(function () {
    Route::get('/lector/dashboard', function () {
        return "Panel del Lector";
    })->name('lector.dashboard');
});

// Auth Breeze
require __DIR__.'/auth.php';
