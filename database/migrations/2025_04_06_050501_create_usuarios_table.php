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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('usuario', 30)->unique();
            $table->string('nombre', 50);
            $table->string('apellido_paterno', 50);
            $table->string('apellido_materno', 50)->nullable();
            $table->string('password');
            $table->foreignUuid('rol_id')->constrained('roles');
            $table->boolean('enable')->default(true); // true=activo, false=inactivo
            $table->datetime('createdAt')->useCurrent(); // Fecha creación en UTC
            $table->datetime('updatedAt')->useCurrent()->useCurrentOnUpdate(); // Fecha actualización
            $table->datetime('deletedAt')->nullable(); // Fecha borrado lógico
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
