<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FiltrosGuardadosSeeder extends Seeder
{
    public function run(): void
    {
        $nombresBase = [
            'Reportes Urgentes',
            'Casos Pendientes',
            'Mis Asignaciones',
            'Problemas de Energía',
            'Internet Esta Semana',
            'Alta Prioridad',
            'Resueltos Hoy',
            'Por Barrio Centro',
            'Casos Antiguos',
            'Requiere Atención',
        ];

        $estados = ['pendiente', 'asignado', 'en_proceso', 'en_revision', 'requiere_informacion', 'resuelto', 'cerrado', 'reabierto'];
        $prioridades = ['baja', 'media', 'alta', 'critica'];

        // Obtener IDs de usuarios, servicios, ciudades
        $usuarios = DB::table('users')->pluck('id')->toArray();
        $servicios = DB::table('servicios')->pluck('id')->toArray();
        $ciudades = DB::table('ciudades')->pluck('id')->toArray();

        if (empty($usuarios)) {
            $this->command->warn('No hay usuarios para crear filtros guardados.');
            return;
        }

        $filtros = [];
        $batchSize = 100;

        for ($i = 0; $i < 500; $i++) {
            $userId = $usuarios[array_rand($usuarios)];

            // Crear combinaciones de filtros variadas
            $filtroData = [];

            // 70% de probabilidad de filtrar por estado
            if (rand(0, 100) < 70) {
                $numEstados = rand(1, 3);
                $estadosSeleccionados = [];
                for ($j = 0; $j < $numEstados; $j++) {
                    $estadosSeleccionados[] = $estados[array_rand($estados)];
                }
                $filtroData['estado'] = array_unique($estadosSeleccionados);
            }

            // 50% de probabilidad de filtrar por prioridad
            if (rand(0, 100) < 50) {
                $filtroData['prioridad'] = $prioridades[array_rand($prioridades)];
            }

            // 40% de probabilidad de filtrar por servicio
            if (rand(0, 100) < 40) {
                $filtroData['servicio_id'] = $servicios[array_rand($servicios)];
            }

            // 30% de probabilidad de filtrar por ciudad
            if (rand(0, 100) < 30) {
                $filtroData['ciudad_id'] = $ciudades[array_rand($ciudades)];
            }

            // 30% de probabilidad de filtrar por fecha
            if (rand(0, 100) < 30) {
                $filtroData['fecha_desde'] = Carbon::now()->subDays(rand(1, 30))->format('Y-m-d');
                $filtroData['fecha_hasta'] = Carbon::now()->format('Y-m-d');
            }

            // 20% de probabilidad de filtrar por asignación
            if (rand(0, 100) < 20) {
                $filtroData['assigned_to'] = $userId;
            }

            $nombre = $nombresBase[array_rand($nombresBase)] . ' ' . ($i + 1);

            $filtros[] = [
                'user_id' => $userId,
                'nombre' => $nombre,
                'filtros' => json_encode($filtroData),
                'es_publico' => rand(0, 100) < 20, // 20% públicos
                'uso_count' => rand(0, 50),
                'created_at' => Carbon::now()->subDays(rand(0, 90)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30)),
            ];

            // Insertar en lotes
            if (count($filtros) >= $batchSize) {
                DB::table('filtros_guardados')->insert($filtros);
                $filtros = [];
            }
        }

        // Insertar registros restantes
        if (!empty($filtros)) {
            DB::table('filtros_guardados')->insert($filtros);
        }

        $this->command->info('500 filtros guardados insertados correctamente.');
    }
}
