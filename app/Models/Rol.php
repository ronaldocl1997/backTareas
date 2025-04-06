<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    protected $table = 'roles';
    protected $keyType = 'string'; // Para UUID
    public $incrementing = false;

    protected $fillable = ['nombre'];

    // Relación con usuarios (opcional pero recomendado)
    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class);
    }
}
