<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Authenticatable implements JWTSubject
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'usuarios';
    protected $keyType = 'string'; // Para UUID
    public $incrementing = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'password',
        'rol_id',
        'enable'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = $model->id ?: (string) Str::uuid();
            $model->enable = true; // Asegura que siempre sea true al crear
        });
    }

    // Relación con rol
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthIdentifierName()
    {
        return 'usuario';
    }
}
