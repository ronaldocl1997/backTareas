<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usuario extends Authenticatable 
{
    use SoftDeletes;

    protected $table = 'usuarios';
    protected $keyType = 'string'; // Para UUID
    public $incrementing = false;

    protected $fillable = [
        'usuario',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'password',
        'rol_id',
        'enable'
    ];

    protected $hidden = ['password'];
    protected $casts = [
        'enable' => 'boolean',
        'createdAt' => 'datetime',
        'updatedAt' => 'datetime',
        'deletedAt' => 'datetime'
    ];

    // Timestamps personalizados
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    // Relación con rol
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }
}
