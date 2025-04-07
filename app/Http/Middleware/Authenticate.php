<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            // Verificación manual del token JWT
            if (!JWTAuth::parseToken()->check()) {
                throw new \Exception('Token inválido');
            }
            
            return $next($request);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Autenticación fallida',
                'error' => $e->getMessage(),
                'token_validation' => JWTAuth::getToken() ? 'present' : 'missing',
                'token_valid' => JWTAuth::getToken() ? JWTAuth::check() : false
            ], 401);
        }
    }
}