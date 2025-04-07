<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str; 

class Categoria extends Model
{
    use SoftDeletes;

    protected $table = 'categorias';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['nombre', 'enable'];
    protected $casts = [
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
            $model->enable = true; // Asegura que siempre sea true al crear
        });
    }
}