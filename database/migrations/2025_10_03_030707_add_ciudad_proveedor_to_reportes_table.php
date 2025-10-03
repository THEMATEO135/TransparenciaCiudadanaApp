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
            $table->foreignId('ciudad_id')->nullable()->after('servicio_id')->constrained('ciudades')->onDelete('set null');
            $table->foreignId('proveedor_id')->nullable()->after('ciudad_id')->constrained('proveedores')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reportes', function (Blueprint $table) {
            $table->dropForeign(['ciudad_id']);
            $table->dropForeign(['proveedor_id']);
            $table->dropColumn(['ciudad_id', 'proveedor_id']);
        });
    }
};
