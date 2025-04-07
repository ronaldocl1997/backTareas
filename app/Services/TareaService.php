<?php

namespace App\Services;

use App\Models\Tarea;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TareaService
{
    public function createTarea(array $data)
    {

        $data['prioridad'] = $data['prioridad'] ?? false;
        
        $validator = Validator::make($data, [
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'prioridad' => ['nullable', 'boolean'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'usuario_id' => ['required', 'exists:usuarios,id']
        ], [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.string' => 'El título debe ser texto.',
            'titulo.max' => 'El título no puede exceder 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'fecha_vencimiento.date' => 'La fecha de vencimiento debe ser una fecha válida.',
            'prioridad.boolean' => 'La prioridad debe ser verdadero o falso.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'usuario_id.required' => 'El usuario es obligatorio.',
            'usuario_id.exists' => 'El usuario seleccionado no existe.'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Tarea::create($data);
    }

    public function getTareas(int $page = 1, int $size = 10, array $filters = [])
    {
        $query = Tarea::where('enable', true)
            ->with(['categoria', 'usuario']);

        if (isset($filters['titulo'])) {
            $query->where('titulo', 'like', '%' . $filters['titulo'] . '%');
        }

        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (isset($filters['prioridad'])) {
            $query->where('prioridad', $filters['prioridad']);
        }

        if (isset($filters['categoria_id'])) {
            $query->where('categoria_id', $filters['categoria_id']);
        }

        if (isset($filters['usuario_id'])) {
            $query->where('usuario_id', $filters['usuario_id']);
        }

        // Nuevo: Filtro por rango de fechas
        if (isset($filters['fecha_desde']) || isset($filters['fecha_hasta'])) {
            $query->where(function($q) use ($filters) {
                if (isset($filters['fecha_desde'])) {
                    $q->whereDate('fecha_vencimiento', '>=', $filters['fecha_desde']);
                }
                if (isset($filters['fecha_hasta'])) {
                    $q->whereDate('fecha_vencimiento', '<=', $filters['fecha_hasta']);
                }
            });
        }

        $query->orderBy('fecha_vencimiento', 'asc')
            ->orderBy('prioridad', 'desc');

        return $query->paginate($size, ['*'], 'page', $page);
    }

    public function updateTarea(string $id, array $data)
    {
        try {
            $validator = Validator::make($data, [
                'titulo' => [
                    'sometimes',
                    'string',
                    'max:255',
                    Rule::unique('tareas')->ignore($id)
                ],
                'descripcion' => 'nullable|string',
                'estado' => [
                    'sometimes',
                    Rule::in(['pendiente', 'en_progreso', 'completada'])
                ],
                'fecha_vencimiento' => 'nullable|date',
                'prioridad' => 'nullable|boolean',
                'enable' => 'nullable|boolean',
                'categoria_id' => 'sometimes|exists:categorias,id',
                'usuario_id' => 'sometimes|exists:usuarios,id'
            ], [
                'titulo.unique' => 'Ya existe una tarea con este título',
                'estado.in' => 'El estado debe ser: pendiente, en_progreso o completada',
                'categoria_id.exists' => 'La categoría seleccionada no existe',
                'usuario_id.exists' => 'El usuario seleccionado no existe'
            ]);

            if ($validator->fails()) {
                \Log::warning('Validación fallida al actualizar tarea', [
                    'id' => $id,
                    'errors' => $validator->errors()->toArray()
                ]);
                throw new ValidationException($validator);
            }

            $tarea = Tarea::find($id);
            
            if (!$tarea) {
                \Log::warning('Intento de actualizar tarea no existente', ['id' => $id]);
                throw new \Exception("La tarea con ID {$id} no existe", 404);
            }

            $tarea->update($data);
            
            // Opcional: Cargar relaciones actualizadas
            $tarea->load(['categoria', 'usuario']);
            
            \Log::info('Tarea actualizada exitosamente', ['id' => $id]);
            return $tarea;

        } catch (\Exception $e) {
            \Log::error('Error al actualizar tarea: ' . $e->getMessage(), [
                'id' => $id,
                'data' => $data,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    public function disableTarea(string $id)
    {
        try {
            $tarea = Tarea::where('id', $id)
                            ->where('enable', true)
                            ->first();

            if (!$tarea) {
                $tareaExistente = Tarea::withTrashed()->find($id);
                \Log::warning('Intento de deshabilitar usuario', [
                    'id' => $id,
                    'exists' => !is_null($tareaExistente),
                    'already_disabled' => $tareaExistente && !$tareaExistente->enable
                ]);
                
                throw new \Exception(
                    is_null($tareaExistente) 
                        ? "La tarea con ID {$id} no existe"
                        : "La tarea ya está deshabilitada",
                    404
                );
            }

            $affected = Tarea::where('id', $id)
                            ->where('enable', true)
                            ->update([
                                'enable' => false,
                                'deletedAt' => now()->toDateTimeString()
                            ]);

            if ($affected === 0) {
                throw new \Exception("No se pudo deshabilitar la tarea", 500);
            }

            $tarea = Tarea::withTrashed()->find($id);
            
            \Log::info('Tarea deshabilitada', [
                'id' => $id,
                'deletedAt' => $tarea->deletedAt,
                'enable' => $tarea->enable
            ]);
            
            return $tarea;

        } catch (\Exception $e) {
            \Log::error('Error al deshabilitar tarea', [
                'id' => $id,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

}