<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        // Insertar servicios por defecto
        DB::table('servicios')->insert([
            ['id' => 1, 'nombre' => 'Energía Eléctrica', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Internet', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Gas Natural', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nombre' => 'Acueducto', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('servicios');
    }
};