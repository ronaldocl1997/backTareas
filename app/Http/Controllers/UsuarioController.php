<?php

namespace App\Http\Controllers;

use App\Services\UsuarioService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsuarioController extends Controller
{
    public function __construct(
        private UsuarioService $usuarioService
    ) {}

    public function create(Request $request)
    {
        try {

            $usuario = $this->usuarioService->createUsuario($request->all());
            
            return response()->json(['message' => 'Usuario creado con éxito', 'usuario' => $usuario], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getUsuarios(Request $request)
    {
        try {

            $page = $request->input('page', 1); 
            $size = $request->input('size', 10);
            
            // Filtros
            $filters = [
                'usuario' => $request->input('usuario'),
                'nombre' => $request->input('nombre'),
                'apellido_paterno' => $request->input('apellido_paterno'),
                'apellido_materno' => $request->input('apellido_materno'),
                'rol_id' => $request->input('rol_id'),
            ];
            
            $filters = array_filter($filters, function($value) {
                return $value !== null;
            });

            $usuarios = $this->usuarioService->getUsuarios($page, $size, $filters);

            $usuarios->getCollection()->transform(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'usuario' => $usuario->usuario,
                    'nombre' => $usuario->nombre,
                    'apellido_paterno' => $usuario->apellido_paterno,
                    'apellido_materno' => $usuario->apellido_materno,
                    'enable' => $usuario->enable,
                    'createdAt' => $usuario->createdAt,
                    'updatedAt' => $usuario->updatedAt,
                    'rol' => [
                        'id' => $usuario->rol->id,
                        'nombre' => $usuario->rol->nombre,
                    ]
                ];
            });

            return response()->json($usuarios);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 400);
        }
    }

    public function updateUsuario(Request $request, string $id)
    {
        try {
            $data = $request->all();
            $usuario = $this->usuarioService->updateUsuario($id, $data);
            return response()->json($usuario);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function disableUsuario(string $id)
    {
        try {
            $usuario = $this->usuarioService->disableUsuario($id);
            return response()->json([
                'message' => 'Usuario deshabilitado correctamente',
                'usuario' => $usuario
            ]);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 404 ? 404 : 400;
            return response()->json(['error' => $e->getMessage()], $statusCode);
        }
    }
    
}