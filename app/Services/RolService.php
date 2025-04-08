<?php

namespace App\Services;

use App\Models\Rol;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; 
use Illuminate\Validation\Rule;

class RolService
{
    // Método para obtener todos los roles sin paginación
    public function getRoles(array $filters = [])
    {
        $query = Rol::query();

        if (isset($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        // Obtener todos los registros sin paginación
        $roles = $query->get();

        return $roles;
    }
}
