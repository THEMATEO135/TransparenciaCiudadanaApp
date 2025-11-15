<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Estado;
use Illuminate\Support\Facades\DB;

class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tabla si existe (útil para re-seeding)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Estado::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $estados = [
            [
                'id' => 1,
                'nombre' => 'pendiente',
                'etiqueta' => 'Pendiente',
                'color' => '#ffc107',
                'icono' => '⏳',
                'es_estado_final' => false,
                'orden' => 1,
                'activo' => true,
                'descripcion' => 'Reporte recibido, pendiente de asignación',
            ],
            [
                'id' => 2,
                'nombre' => 'asignado',
                'etiqueta' => 'Asignado',
                'color' => '#17a2b8',
                'icono' => '👤',
                'es_estado_final' => false,
                'orden' => 2,
                'activo' => true,
                'descripcion' => 'Reporte asignado a un operador',
            ],
            [
                'id' => 3,
                'nombre' => 'en_proceso',
                'etiqueta' => 'En Proceso',
                'color' => '#007bff',
                'icono' => '🔄',
                'es_estado_final' => false,
                'orden' => 3,
                'activo' => true,
                'descripcion' => 'Reporte en proceso de resolución',
            ],
            [
                'id' => 4,
                'nombre' => 'resuelto',
                'etiqueta' => 'Resuelto',
                'color' => '#28a745',
                'icono' => '✅',
                'es_estado_final' => true,
                'orden' => 4,
                'activo' => true,
                'descripcion' => 'Reporte resuelto exitosamente',
            ],
            [
                'id' => 5,
                'nombre' => 'rechazado',
                'etiqueta' => 'Rechazado',
                'color' => '#dc3545',
                'icono' => '❌',
                'es_estado_final' => true,
                'orden' => 5,
                'activo' => true,
                'descripcion' => 'Reporte rechazado o no procede',
            ],
            [
                'id' => 6,
                'nombre' => 'cerrado',
                'etiqueta' => 'Cerrado',
                'color' => '#6c757d',
                'icono' => '🔒',
                'es_estado_final' => true,
                'orden' => 6,
                'activo' => true,
                'descripcion' => 'Reporte cerrado por el sistema',
            ],
        ];

        foreach ($estados as $estado) {
            Estado::create($estado);
        }

        $this->command->info('✅ Estados creados exitosamente');
    }
}
