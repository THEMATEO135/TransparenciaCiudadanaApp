<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('correo');
            $table->string('telefono');
            $table->unsignedBigInteger('servicio_id');
            $table->text('descripcion');
            $table->string('direccion')->nullable();
            $table->string('localidad')->nullable();
            $table->string('barrio')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reportes');
    }
};