<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada'])->default('pendiente');
            $table->date('fecha_vencimiento')->nullable();
            $table->boolean('prioridad')->default(false);
            $table->boolean('enable')->default(true);
            
            // Relación con categorías
            $table->foreignUuid('categoria_id')
                ->constrained('categorias')
                ->onDelete('cascade');
            
            // Relación con usuarios
            $table->foreignUuid('usuario_id')
                ->constrained('usuarios')
                ->onDelete('cascade');
            
            // Timestamps consistentes con tus otras tablas
            $table->datetime('createdAt')->useCurrent();
            $table->datetime('updatedAt')->useCurrent()->useCurrentOnUpdate();
            $table->datetime('deletedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
