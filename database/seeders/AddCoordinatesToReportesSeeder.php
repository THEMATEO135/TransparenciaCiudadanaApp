<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reporte;

class AddCoordinatesToReportesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Coordenadas de ciudades principales de Colombia
        $coordenadas = [
            ['lat' => 4.6097, 'lng' => -74.0817], // Bogotá
            ['lat' => 6.2518, 'lng' => -75.5636], // Medellín
            ['lat' => 3.4516, 'lng' => -76.5320], // Cali
            ['lat' => 11.0041, 'lng' => -74.8070], // Barranquilla
            ['lat' => 7.1193, 'lng' => -73.1227], // Bucaramanga
            ['lat' => 4.5389, 'lng' => -75.6659], // Pereira
            ['lat' => 10.3910, 'lng' => -75.4794], // Cartagena
            ['lat' => 4.8133, 'lng' => -75.6961], // Manizales
        ];

        // Obtener todos los reportes
        $reportes = Reporte::all();

        if ($reportes->isEmpty()) {
            $this->command->warn('No hay reportes en la base de datos.');
            return;
        }

        $this->command->info("Agregando coordenadas a {$reportes->count()} reportes...");

        foreach ($reportes as $index => $reporte) {
            // Asignar coordenadas de forma cíclica
            $coord = $coordenadas[$index % count($coordenadas)];

            // Agregar variación aleatoria pequeña para que no estén exactamente en el mismo punto
            $reporte->latitude = $coord['lat'] + (rand(-100, 100) / 10000);
            $reporte->longitude = $coord['lng'] + (rand(-100, 100) / 10000);
            $reporte->save();

            $this->command->info("Reporte #{$reporte->id}: Lat {$reporte->latitude}, Lng {$reporte->longitude}");
        }

        $this->command->info('¡Coordenadas agregadas exitosamente!');
    }
}
