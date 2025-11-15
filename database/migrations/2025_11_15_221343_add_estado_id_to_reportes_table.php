<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar columna estado_id (nullable temporalmente)
        Schema::table('reportes', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_id')->nullable()->after('descripcion');
            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('restrict');
        });

        // Migrar datos del campo 'estado' al nuevo 'estado_id'
        // Mapear los valores string a los IDs correspondientes
        $estadosMap = [
            'pendiente' => 1,
            'asignado' => 2,
            'en_proceso' => 3,
            'resuelto' => 4,
            'rechazado' => 5,
            'cerrado' => 6,
        ];

        foreach ($estadosMap as $estadoNombre => $estadoId) {
            DB::table('reportes')
                ->where('estado', $estadoNombre)
                ->update(['estado_id' => $estadoId]);
        }

        // Verificar que todos los reportes tengan estado_id
        $reportesSinEstado = DB::table('reportes')->whereNull('estado_id')->count();
        if ($reportesSinEstado > 0) {
            // Asignar estado 'pendiente' por defecto a los que no tengan
            DB::table('reportes')
                ->whereNull('estado_id')
                ->update(['estado_id' => 1]);
        }

        // Hacer el campo estado_id obligatorio
        Schema::table('reportes', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_id')->nullable(false)->change();
        });

        // Eliminar la columna antigua 'estado'
        Schema::table('reportes', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recrear la columna estado
        Schema::table('reportes', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'en_proceso', 'resuelto'])->default('pendiente')->after('descripcion');
        });

        // Migrar datos de estado_id de vuelta a estado
        DB::table('reportes')
            ->join('estados', 'reportes.estado_id', '=', 'estados.id')
            ->update(['reportes.estado' => DB::raw('estados.nombre')]);

        // Eliminar estado_id
        Schema::table('reportes', function (Blueprint $table) {
            $table->dropForeign(['estado_id']);
            $table->dropColumn('estado_id');
        });
    }
};
