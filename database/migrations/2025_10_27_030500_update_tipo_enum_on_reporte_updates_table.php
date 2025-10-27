<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE reporte_updates
            MODIFY COLUMN tipo ENUM(
                'comentario',
                'cambio_estado',
                'imagen',
                'asignacion',
                'reasignacion',
                'sistema',
                'cambio_prioridad',
                'actualizacion'
            ) DEFAULT 'comentario'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE reporte_updates
            MODIFY COLUMN tipo ENUM(
                'comentario',
                'cambio_estado',
                'imagen',
                'asignacion',
                'reasignacion',
                'sistema'
            ) DEFAULT 'comentario'
        ");
    }
};
