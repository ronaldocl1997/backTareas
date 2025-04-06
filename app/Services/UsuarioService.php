<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule; 

class UsuarioService
{
    /**
     * Crear un nuevo usuario con validaciones.
     *
     * @param array $data
     * @return Usuario
     * @throws ValidationException
     */
    public function createUsuario(array $data)
    {
        // Validación (incluye regla 'unique')
        $validator = Validator::make($data, [
            'usuario' => ['required', 'string', 'max:30', 'unique:usuarios'],
            'nombre' => 'required|string|max:50',
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'nullable|string|max:50',
            'password' => 'required|string|min:8',
            'rol_id' => ['required', 'exists:roles,id'],
            'enable' => 'nullable|boolean',
        ], [
            // Mensajes personalizados para validación
            'usuario.required' => 'El campo de usuario es obligatorio.',
            'usuario.string' => 'El usuario debe ser una cadena de caracteres.',
            'usuario.max' => 'El nombre de usuario no puede tener más de 30 caracteres.',
            'usuario.unique' => 'Este nombre de usuario ya está registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de caracteres.',
            'nombre.max' => 'El nombre no puede tener más de 50 caracteres.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.string' => 'El apellido paterno debe ser una cadena de caracteres.',
            'apellido_paterno.max' => 'El apellido paterno no puede tener más de 50 caracteres.',
            'apellido_materno.string' => 'El apellido materno debe ser una cadena de caracteres.',
            'apellido_materno.max' => 'El apellido materno no puede tener más de 50 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'rol_id.required' => 'El rol es obligatorio.',
            'rol_id.exists' => 'El rol seleccionado no existe.',
            'enable.boolean' => 'El campo de habilitación debe ser verdadero o falso.',
        ]);

        // Si la validación falla, lanza la excepción con los errores
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Crear el usuario
        $usuario = Usuario::create([
            'id' => (string) \Str::uuid(),
            'usuario' => $data['usuario'],
            'nombre' => $data['nombre'],
            'apellido_paterno' => $data['apellido_paterno'],
            'apellido_materno' => $data['apellido_materno'],
            'password' => bcrypt($data['password']),
            'rol_id' => $data['rol_id'],
            'enable' => $data['enable'] ?? true,
        ]);

        return $usuario;
    }

    /**
     * Obtener usuarios con filtros y paginación, pero solo los usuarios habilitados (enable = true).
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    /**
     * Obtener usuarios con filtros y paginación.
     *
     * @param int $page
     * @param int $size
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUsuarios(int $page = 1, int $size = 10, array $filters = [])
    {
        // Consulta base
        $query = Usuario::where('enable', true);  // Solo usuarios habilitados

        // Aquí puedes agregar filtros adicionales, si es necesario
        if (isset($filters['usuario'])) {
            $query->where('usuario', 'like', '%' . $filters['usuario'] . '%');
        }

        if (isset($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        if (isset($filters['apellido_paterno'])) {
            $query->where('apellido_paterno', 'like', '%' . $filters['apellido_paterno'] . '%');
        }

        if (isset($filters['apellido_materno'])) {
            $query->where('apellido_materno', 'like', '%' . $filters['apellido_materno'] . '%');
        }

        if (isset($filters['rol_id'])) {
            $query->where('rol_id', $filters['rol_id']);
        }


        // Paginación
        $usuarios = $query->paginate($size, ['*'], 'page', $page);

        return $usuarios;
    }

    /**
     * Actualiza un usuario con validaciones.
     * 
     * @param string $id
     * @param array $data
     * @return Usuario
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function updateUsuario(string $id, array $data)
    {
        try {
            // Validación
            $validator = Validator::make($data, [
                'usuario' => [
                    'sometimes',
                    'string',
                    'max:30',
                    Rule::unique('usuarios')->ignore($id),
                ],
                'nombre' => 'sometimes|string|max:50',
                'apellido_paterno' => 'sometimes|string|max:50',
                'apellido_materno' => 'nullable|string|max:50',
                'password' => 'sometimes|string|min:8',
                'rol_id' => 'sometimes|exists:roles,id',
                'enable' => 'nullable|boolean',
            ], [
                'usuario.unique' => 'El nombre de usuario ya está en uso.',
                'rol_id.exists' => 'El rol seleccionado no existe.',
            ]);

            if ($validator->fails()) {
                \Log::warning('Validación fallida al actualizar usuario', [
                    'id' => $id,
                    'errors' => $validator->errors()->toArray()
                ]);
                throw new ValidationException($validator);
            }

            // Buscar usuario
            $usuario = Usuario::find($id);
            
            if (!$usuario) {
                \Log::warning('Intento de actualizar usuario no existente', ['id' => $id]);
                throw new \Exception("El usuario con ID {$id} no existe", 404);
            }

            // Actualizar campos
            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            $usuario->update($data);
            
            \Log::info('Usuario actualizado exitosamente', ['id' => $id]);
            return $usuario;

        } catch (\Exception $e) {
            \Log::error('Error al actualizar usuario: ' . $e->getMessage(), [
                'id' => $id,
                'data' => $data,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Deshabilita un usuario (eliminación lógica)
     * 
     * @param string $id
     * @return Usuario
     * @throws \Exception
     */
    public function disableUsuario(string $id)
    {
        try {
            $usuario = Usuario::where('id', $id)
                            ->where('enable', true) // Solo si está actualmente activo
                            ->first();

            if (!$usuario) {
                \Log::warning('Intento de deshabilitar usuario inexistente o ya inactivo', ['id' => $id]);
                throw new \Exception(
                    is_null(Usuario::find($id)) 
                        ? "El usuario con ID {$id} no existe"
                        : "El usuario ya está deshabilitado",
                    404
                );
            }

            $usuario->update(['enable' => false]);
            \Log::info('Usuario deshabilitado', ['id' => $id]);
            
            return $usuario;

        } catch (\Exception $e) {
            \Log::error('Error al deshabilitar usuario: ' . $e->getMessage(), ['id' => $id]);
            throw $e;
        }
    }

    public function enableUsuario(string $id) 
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->update(['enable' => true]);
        return $usuario;
    }
}
