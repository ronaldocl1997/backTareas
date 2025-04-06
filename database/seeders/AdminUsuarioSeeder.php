<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolAdmin = Rol::where('nombre', 'admin')->first();

        Usuario::create([
            'id' => Str::uuid(),
            'usuario' => 'admin',
            'nombre' => 'Administrador',
            'apellido_paterno' => 'Sistema',
            'password' => Hash::make('Admin123*'), // Cambia esta contraseña
            'rol_id' => $rolAdmin->id,
            'enable' => true,
            'createdAt' => now(),
            'updatedAt' => now()
        ]);
    }
}
