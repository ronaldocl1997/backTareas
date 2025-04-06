<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return response()->json([
        'message' => 'Bienvenido a la API. Use /api/... para acceder a los endpoints.',
        'status' => 404
    ], 404);
});

