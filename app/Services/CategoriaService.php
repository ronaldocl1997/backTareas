<?php

namespace App\Services;

use App\Models\Categoria;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; 
use Illuminate\Validation\Rule;

class CategoriaService
{
    public function getCategorias(int $page = 1, int $size = 10, array $filters = [])
    {
        $query = Categoria::where('enable', true);

        if (isset($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        $categorias = $query->paginate($size, ['*'], 'page', $page);

        return $categorias;
    }

    public function createCategoria(array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => ['required', 'string', 'max:100', 'unique:categorias'],
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de caracteres.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'nombre.unique' => 'Esta categoría ya está registrada.'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Categoria::create([
            'nombre' => $data['nombre']
        ]);
    }

    public function updateCategoria(string $id, array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('categorias')->ignore($id)
            ]
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de caracteres.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'nombre.unique' => 'Esta categoría ya está registrada.'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $categoria = Categoria::find($id);

        if (!$categoria) {
            \Log::warning('Intento de actualizar categoría no existente', ['id' => $id]);
            throw new \Exception("La categoría con ID {$id} no existe", 404);
        }

        $categoria->update($data);
        return $categoria;
    }

    public function disableCategoria(string $id)
    {
        try {

            $categoria = Categoria::where('id', $id)
                                ->where('enable', true)
                                ->first();

            if (!$categoria) {
                $categoriaExistente = Categoria::withTrashed()->find($id);
                \Log::warning('Intento de deshabilitar categoría', [
                    'id' => $id,
                    'exists' => !is_null($categoriaExistente),
                    'already_disabled' => $categoriaExistente && !$categoriaExistente->enable
                ]);
                
                throw new \Exception(
                    is_null($categoriaExistente) 
                        ? "La categoría con ID {$id} no existe"
                        : "La categoría ya está deshabilitada",
                    404
                );
            }

            // Actualización atómica
            $affected = Categoria::where('id', $id)
                                ->where('enable', true)
                                ->update([
                                    'enable' => false,
                                    'deletedAt' => now()->toDateTimeString()
                                ]);

            if ($affected === 0) {
                throw new \Exception("No se pudo deshabilitar la categoría", 500);
            }

            // Recargar modelo actualizado
            $categoria = Categoria::withTrashed()->find($id);
            
            \Log::info('Categoría deshabilitada', [
                'id' => $id,
                'deletedAt' => $categoria->deletedAt,
                'enable' => $categoria->enable
            ]);
            
            return $categoria;

        } catch (\Exception $e) {
            \Log::error('Error al deshabilitar categoría', [
                'id' => $id,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}