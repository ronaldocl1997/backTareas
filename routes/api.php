<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CategoriaController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//---------apis para usuarios------------------------------------------------------//

Route::post('/usuarios', [UsuarioController::class, 'create']);

Route::get('/usuarios', [UsuarioController::class, 'getUsuarios']);

Route::put('/usuarios/{id}', [UsuarioController::class, 'updateUsuario']);

Route::patch('/usuarios/{id}/disable', [UsuarioController::class, 'disableUsuario']);

Route::patch('/usuarios/{id}/enable', [UsuarioController::class, 'enableUsuario']);

//----------------------------------------------------------------------------------//

//---------apis categorias----------------------------------------------------------//

Route::post('/categoria', [CategoriaController::class, 'create']);

Route::get('/categoria', [CategoriaController::class, 'getCategorias']);

Route::put('/categoria/{id}', [CategoriaController::class, 'updateCategoria']);

Route::patch('/categoria/{id}/disable', [CategoriaController::class, 'disableCategoria']);
//----------------------------------------------------------------------------------------//









