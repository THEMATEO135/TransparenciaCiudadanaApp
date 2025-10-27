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
        Schema::create('reporte_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporte_id');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Usuario que creó la actualización (admin/operador)');
            $table->enum('tipo', ['comentario', 'cambio_estado', 'imagen', 'asignacion', 'reasignacion', 'sistema'])->default('comentario');
            $table->text('contenido')->nullable();
            $table->string('archivo_url')->nullable()->comment('URL de imagen o archivo adjunto');
            $table->boolean('visible_ciudadano')->default(true)->comment('Si el ciudadano puede ver esta actualización');
            $table->string('estado_anterior')->nullable();
            $table->string('estado_nuevo')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('reporte_id')->references('id')->on('reportes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Índices
            $table->index('reporte_id');
            $table->index('tipo');
            $table->index('visible_ciudadano');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_updates');
    }
};
