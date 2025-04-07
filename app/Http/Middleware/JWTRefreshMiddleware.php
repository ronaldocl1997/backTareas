<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTRefreshMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            // Verifica el token sin invalidarlo automáticamente
            $token = JWTAuth::parseToken();
            $token->getPayload(); // Verifica la validez del token
            
            return $next($request);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token inválido o expirado',
                'error' => $e->getMessage()
            ], 401);
        }
    }
}
