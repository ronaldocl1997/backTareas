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
            // Llamar al servicio para crear el usuario
            $usuario = $this->usuarioService->createUsuario($request->all());
            
            return response()->json(['message' => 'Usuario creado con éxito', 'usuario' => $usuario], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

     /**
     * Obtener los usuarios con filtros y paginación, solo aquellos habilitados (enable = true).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsuarios(Request $request)
    {
        try {
            // Obtener parámetros de paginación, con valores predeterminados
            $page = $request->input('page', 1);  // Página por defecto es 1
            $size = $request->input('size', 10); // Tamaño por defecto es 10
            
            // Obtener parámetros de filtrado
            $filters = [
                'usuario' => $request->input('usuario'),
                'nombre' => $request->input('nombre'),
                'apellido_paterno' => $request->input('apellido_paterno'),
                'apellido_materno' => $request->input('apellido_materno'),
                'rol_id' => $request->input('rol_id'),
            ];
            
            // Filtrar valores nulos
            $filters = array_filter($filters, function($value) {
                return $value !== null;
            });

            // Llamar al servicio para obtener los usuarios con paginación y filtros
            $usuarios = $this->usuarioService->getUsuarios($page, $size, $filters);

            return response()->json($usuarios);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

        /**
     * Actualiza un usuario existente.
     * 
     * @param Request $request
     * @param string $id (UUID del usuario)
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Eliminación lógica de un usuario (enable = false)
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
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