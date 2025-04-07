<?php
namespace App\Http\Controllers;

use App\Services\TareaService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class TareaController extends Controller
{
    public function __construct(
        private TareaService $tareaService
    ) {}

    public function create(Request $request)
    {
        try {
            $tarea = $this->tareaService->createTarea($request->all());
            
            return response()->json([
                'message' => 'Tarea creada con éxito',
                'tarea' => $tarea
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function getTareas(Request $request)
    {
        try {
            $page = $request->input('page', 1); 
            $size = $request->input('size', 10);
            
            // Filtros
            $filters = [
                'titulo' => $request->input('titulo'),
                'estado' => $request->input('estado'),
                'prioridad' => $request->input('prioridad'),
                'categoria_id' => $request->input('categoria_id'),
                'usuario_id' => $request->input('usuario_id'),
                'fecha_desde' => $request->input('fecha_desde'),
                'fecha_hasta' => $request->input('fecha_hasta')
            ];
            
            $filters = array_filter($filters, function($value) {
                return $value !== null;
            });

            $tareas = $this->tareaService->getTareas($page, $size, $filters);

            $tareas->getCollection()->transform(function ($tarea) {
                return [
                    'id' => $tarea->id,
                    'titulo' => $tarea->titulo,
                    'descripcion' => $tarea->descripcion,
                    'estado' => $tarea->estado,
                    'fecha_vencimiento' => $tarea->fecha_vencimiento,
                    'prioridad' => $tarea->prioridad,
                    'enable' => $tarea->enable,
                    'createdAt' => $tarea->createdAt,
                    'updatedAt' => $tarea->updatedAt,
                    'categoria' => $tarea->categoria ? [
                        'id' => $tarea->categoria->id,
                        'nombre' => $tarea->categoria->nombre,
                    ] : null,
                    'usuario' => $tarea->usuario ? [
                        'id' => $tarea->usuario->id,
                        'nombre' => $tarea->usuario->nombre,
                    ] : null
                ];
            });

            return response()->json($tareas);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 400);
        }
    }

    public function updateTarea(Request $request, string $id)
    {
        try {
            $data = $request->all();
            $tarea = $this->tareaService->updateTarea($id, $data);
            return response()->json($tarea);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 400;
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], $statusCode);
        }
    }

    public function disableTarea(string $id)
    {
        try {
            $tarea = $this->tareaService->disableTarea($id);
            return response()->json([
                'message' => 'Tarea deshabilitado correctamente',
                'tarea' => $tarea
            ]);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 404 ? 404 : 400;
            return response()->json(['error' => $e->getMessage()], $statusCode);
        }
    }
}