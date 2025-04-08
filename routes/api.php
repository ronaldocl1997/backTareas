<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas API para tu aplicación.
|
*/

// Rutas públicas (sin autenticación)
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/usuarios', [UsuarioController::class, 'create'])->name('api.usuarios.create');

// Rutas protegidas (requieren token JWT válido)
Route::middleware('auth:api')->group(function () {
    
    // Grupo de rutas para usuarios
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'getUsuarios'])->name('api.usuarios.index');
        Route::put('/{id}', [UsuarioController::class, 'updateUsuario'])->name('api.usuarios.update');
        Route::patch('/{id}/disable', [UsuarioController::class, 'disableUsuario'])->name('api.usuarios.disable');
        Route::patch('/{id}/enable', [UsuarioController::class, 'enableUsuario'])->name('api.usuarios.enable');
    });

    // Grupo de rutas para categorías
    Route::prefix('categoria')->group(function () {
        Route::post('/', [CategoriaController::class, 'create'])->name('api.categorias.create');
        Route::get('/', [CategoriaController::class, 'getCategorias'])->name('api.categorias.index');
        Route::put('/{id}', [CategoriaController::class, 'updateCategoria'])->name('api.categorias.update');
        Route::patch('/{id}/disable', [CategoriaController::class, 'disableCategoria'])->name('api.categorias.disable');
    });

    // Grupo de rutas para tareas
    Route::prefix('tarea')->group(function () {
        Route::post('/', [TareaController::class, 'create'])->name('api.tareas.create');
        Route::get('/', [TareaController::class, 'getTareas'])->name('api.tareas.index');
        Route::put('/{id}', [TareaController::class, 'updateTarea'])->name('api.tareas.update');
        Route::patch('/{id}/disable', [TareaController::class, 'disableTarea'])->name('api.tareas.disable');
    });

    // Rutas de autenticación
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.refresh');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');

    // Rutas para roles
    Route::prefix('roles')->group(function () {
        Route::get('/', [RolController::class, 'getRoles'])->name('api.roles.index');
    });
});