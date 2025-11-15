<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Seed the application's database with test data.
     *
     * Este seeder inserta 500 registros de prueba en cada tabla.
     * Asegúrate de que ya existen datos en: users, ciudades, proveedores, servicios, ciudad_proveedor_servicio
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('Iniciando inserción de datos de prueba');
        $this->command->info('========================================');

        $this->call([
            ReportesSeeder::class,
            PlantillasRespuestaSeeder::class,
            ReporteUpdatesSeeder::class,
            ReporteFeedbacksSeeder::class,
            FiltrosGuardadosSeeder::class,
            ActivityLogsSeeder::class,
            NotificationsSeeder::class,
        ]);

        $this->command->info('========================================');
        $this->command->info('Datos de prueba insertados exitosamente');
        $this->command->info('========================================');
    }
}
