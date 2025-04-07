<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => auth('api')->user(),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        try {
            // Obtiene el token actual
            $token = JWTAuth::getToken();
            
            if (!$token) {
                return response()->json(['error' => 'Token no proporcionado'], 401);
            }

            // Genera nuevo token
            $newToken = JWTAuth::refresh($token);
            
            return response()->json([
                'status' => 'success',
                'token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ]);
            
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token expirado',
                'solution' => 'Por favor inicie sesión nuevamente'
            ], 401);
            
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token inválido'
            ], 401);
            
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar el token'
            ], 500);
        }
    }

    public function me()
    {
        return response()->json(Auth::user());
    }
}
