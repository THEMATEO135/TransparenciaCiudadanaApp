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
        Schema::create('plantillas_respuesta', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('asunto');
            $table->text('contenido')->comment('Puede contener variables: {nombre_ciudadano}, {fecha_estimada}, {barrio}, etc.');
            $table->enum('tipo', ['resolucion', 'informacion', 'mantenimiento', 'escalado', 'otro'])->default('informacion');
            $table->boolean('activa')->default(true);
            $table->integer('uso_count')->default(0)->comment('Contador de usos');
            $table->timestamps();

            // Índices
            $table->index('tipo');
            $table->index('activa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantillas_respuesta');
    }
};
