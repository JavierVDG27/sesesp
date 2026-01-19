<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Esta es la ruta que consumirá Android
Route::post('/login-movil', [AuthController::class, 'loginMovil']);