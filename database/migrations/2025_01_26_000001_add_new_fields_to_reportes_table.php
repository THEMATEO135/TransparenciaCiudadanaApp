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
        Schema::table('reportes', function (Blueprint $table) {
            // Sistema de priorización
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media')->after('estado');

            // Asignación de operadores
            $table->unsignedBigInteger('assigned_to')->nullable()->after('prioridad');
            $table->timestamp('assigned_at')->nullable()->after('assigned_to');
            $table->timestamp('deadline')->nullable()->after('assigned_at');
            $table->integer('sla_hours')->nullable()->after('deadline')->comment('SLA en horas');

            // Sistema de duplicados
            $table->unsignedBigInteger('parent_id')->nullable()->after('sla_hours')->comment('Reporte padre si es duplicado');
            $table->integer('duplicados_count')->default(0)->after('parent_id')->comment('Cantidad de duplicados');

            // Imágenes
            $table->json('imagenes')->nullable()->after('duplicados_count')->comment('Array de URLs de imágenes');

            // Estados adicionales - modificar el enum existente
            $table->enum('estado', ['pendiente', 'asignado', 'en_proceso', 'en_revision', 'requiere_informacion', 'resuelto', 'cerrado', 'reabierto'])->default('pendiente')->change();

            // Foreign keys
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('reportes')->onDelete('cascade');

            // Índices
            $table->index('prioridad');
            $table->index('assigned_to');
            $table->index('parent_id');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reportes', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['parent_id']);

            $table->dropColumn([
                'prioridad',
                'assigned_to',
                'assigned_at',
                'deadline',
                'sla_hours',
                'parent_id',
                'duplicados_count',
                'imagenes'
            ]);
        });
    }
};
