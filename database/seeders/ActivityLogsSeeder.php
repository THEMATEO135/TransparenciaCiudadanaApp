<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityLogsSeeder extends Seeder
{
    public function run(): void
    {
        $acciones = ['create', 'update', 'delete', 'login', 'logout', 'view', 'assign', 'close', 'reopen'];
        $modelTypes = ['Reporte', 'User', 'PlantillaRespuesta', 'Proveedor', 'Ciudad', null];

        $descripciones = [
            'create' => [
                'Creó un nuevo reporte en el sistema',
                'Registró una nueva plantilla de respuesta',
                'Agregó un nuevo usuario al sistema',
                'Creó un nuevo proveedor',
                'Registró una nueva ciudad',
            ],
            'update' => [
                'Actualizó la información del reporte',
                'Modificó los datos del usuario',
                'Editó la plantilla de respuesta',
                'Cambió el estado del reporte',
                'Actualizó la prioridad del caso',
            ],
            'delete' => [
                'Eliminó un registro del sistema',
                'Removió una plantilla de respuesta',
                'Eliminó un filtro guardado',
                'Borró información del sistema',
            ],
            'login' => [
                'Inició sesión en el sistema',
                'Accedió al panel de administración',
                'Se autenticó correctamente',
            ],
            'logout' => [
                'Cerró sesión en el sistema',
                'Salió del panel de administración',
                'Finalizó su sesión',
            ],
            'view' => [
                'Visualizó el detalle del reporte',
                'Consultó la información del usuario',
                'Accedió al dashboard de estadísticas',
                'Revisó los reportes asignados',
            ],
            'assign' => [
                'Asignó el reporte a un operador',
                'Reasignó el caso a otro técnico',
                'Cambió la asignación del reporte',
            ],
            'close' => [
                'Cerró el reporte como resuelto',
                'Finalizó el caso exitosamente',
                'Marcó el reporte como cerrado',
            ],
            'reopen' => [
                'Reabrió el reporte por nueva incidencia',
                'Reactivó el caso',
                'Cambió el estado a reabierto',
            ],
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        ];

        // Obtener IDs de usuarios y reportes
        $usuarios = DB::table('users')->pluck('id')->toArray();
        $reportes = DB::table('reportes')->pluck('id')->toArray();

        if (empty($usuarios)) {
            $this->command->warn('No hay usuarios para crear logs de actividad.');
            return;
        }

        $logs = [];
        $batchSize = 100;

        for ($i = 0; $i < 500; $i++) {
            $userId = $usuarios[array_rand($usuarios)];
            $action = $acciones[array_rand($acciones)];
            $modelType = $modelTypes[array_rand($modelTypes)];

            $description = $descripciones[$action][array_rand($descripciones[$action])];

            $modelId = null;
            if ($modelType === 'Reporte' && !empty($reportes)) {
                $modelId = $reportes[array_rand($reportes)];
            } elseif ($modelType === 'User') {
                $modelId = $usuarios[array_rand($usuarios)];
            }

            $changes = null;
            if (in_array($action, ['update', 'assign'])) {
                $changes = json_encode([
                    'campo_modificado' => 'estado',
                    'valor_anterior' => 'pendiente',
                    'valor_nuevo' => 'en_proceso',
                ]);
            }

            $logs[] = [
                'user_id' => $userId,
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'description' => $description,
                'changes' => $changes,
                'ip_address' => rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 255),
                'user_agent' => $userAgents[array_rand($userAgents)],
                'created_at' => Carbon::now()->subDays(rand(0, 90))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                'updated_at' => Carbon::now()->subDays(rand(0, 90))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
            ];

            // Insertar en lotes
            if (count($logs) >= $batchSize) {
                DB::table('activity_logs')->insert($logs);
                $logs = [];
            }
        }

        // Insertar registros restantes
        if (!empty($logs)) {
            DB::table('activity_logs')->insert($logs);
        }

        $this->command->info('500 logs de actividad insertados correctamente.');
    }
}
