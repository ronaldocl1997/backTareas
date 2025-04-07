<?php

namespace App\Models;

use Illuminate\Support\Str; // Importar Str para UUID
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarea extends Model
{
    use SoftDeletes;

    public $incrementing = false; // Importante para UUID
    protected $keyType = 'string'; // Importante para UUID

    protected $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'fecha_vencimiento',
        'prioridad',
        'enable',
        'categoria_id',
        'usuario_id'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'prioridad' => 'boolean',
        'enable' => 'boolean',
        'createdAt' => 'datetime',
        'updatedAt' => 'datetime',
        'deletedAt' => 'datetime'
    ];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = $model->id ?: (string) Str::uuid();
            $model->enable = true; // Fuerza enable = true al crear
            $model->estado = $model->estado ?: 'pendiente'; // Valor por defecto
        });
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
    
}