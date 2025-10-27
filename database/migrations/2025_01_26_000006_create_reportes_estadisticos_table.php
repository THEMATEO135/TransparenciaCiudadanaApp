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
        Schema::create('reportes_estadisticos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('frecuencia', ['diario', 'semanal', 'mensual', 'personalizado'])->default('mensual');
            $table->json('configuracion')->comment('Filtros, gráficos a incluir, etc.');
            $table->json('destinatarios')->comment('Array de emails');
            $table->boolean('activo')->default(true);
            $table->timestamp('ultima_ejecucion')->nullable();
            $table->timestamp('proxima_ejecucion')->nullable();
            $table->timestamps();

            // Índices
            $table->index('activo');
            $table->index('proxima_ejecucion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_estadisticos');
    }
};
