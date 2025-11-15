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
        Schema::create('estados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique()->comment('Nombre interno del estado (ej: pendiente, en_proceso)');
            $table->string('etiqueta', 50)->comment('Etiqueta visible para el usuario (ej: Pendiente, En Proceso)');
            $table->string('color', 7)->default('#6c757d')->comment('Color hexadecimal para mostrar el estado');
            $table->string('icono', 10)->nullable()->comment('Emoji o icono para el estado');
            $table->boolean('es_estado_final')->default(false)->comment('Indica si es un estado final (no puede cambiar)');
            $table->integer('orden')->default(0)->comment('Orden de visualización');
            $table->boolean('activo')->default(true)->comment('Si el estado está activo para usarse');
            $table->text('descripcion')->nullable()->comment('Descripción del estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados');
    }
};
