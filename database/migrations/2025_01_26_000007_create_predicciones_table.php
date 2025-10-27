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
        Schema::create('predicciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('servicio_id');
            $table->unsignedBigInteger('ciudad_id')->nullable();
            $table->string('zona')->nullable()->comment('Barrio o localidad específica');
            $table->enum('tipo_prediccion', ['alta_probabilidad', 'patron_detectado', 'mantenimiento_sugerido'])->default('alta_probabilidad');
            $table->decimal('probabilidad', 5, 2)->comment('Porcentaje de probabilidad 0-100');
            $table->text('descripcion');
            $table->json('factores')->comment('Factores que influyen: clima, histórico, etc.');
            $table->timestamp('fecha_prediccion')->comment('Fecha para la cual se predice el problema');
            $table->boolean('alerta_enviada')->default(false);
            $table->boolean('se_cumplio')->nullable()->comment('¿La predicción se cumplió?');
            $table->timestamps();

            // Foreign keys
            $table->foreign('servicio_id')->references('id')->on('servicios')->onDelete('cascade');
            $table->foreign('ciudad_id')->references('id')->on('ciudades')->onDelete('cascade');

            // Índices
            $table->index('fecha_prediccion');
            $table->index('alerta_enviada');
            $table->index('probabilidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predicciones');
    }
};
