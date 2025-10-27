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
        Schema::create('reporte_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporte_id')->unique();
            $table->boolean('resuelto')->nullable()->comment('¿El ciudadano confirma que está resuelto?');
            $table->integer('calificacion')->nullable()->comment('Calificación 1-5 estrellas');
            $table->integer('nps')->nullable()->comment('Net Promoter Score 0-10');
            $table->text('comentario')->nullable();
            $table->timestamp('respondido_at')->nullable();
            $table->string('token')->unique()->comment('Token único para verificación');
            $table->timestamps();

            // Foreign key
            $table->foreign('reporte_id')->references('id')->on('reportes')->onDelete('cascade');

            // Índices
            $table->index('resuelto');
            $table->index('calificacion');
            $table->index('nps');
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_feedbacks');
    }
};
