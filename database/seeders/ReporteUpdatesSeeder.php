<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteUpdatesSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = ['comentario', 'cambio_estado', 'imagen', 'asignacion', 'reasignacion', 'sistema'];
        $estados = ['pendiente', 'asignado', 'en_proceso', 'en_revision', 'requiere_informacion', 'resuelto', 'cerrado', 'reabierto'];

        $contenidos = [
            'Se ha recibido el reporte y está siendo revisado por nuestro equipo.',
            'El técnico ha sido asignado y visitará la zona en las próximas 24 horas.',
            'Se detectó el problema en la infraestructura local. Iniciando reparación.',
            'Requiere aprobación de gerencia para proceder con la solución.',
            'El ciudadano ha proporcionado información adicional sobre el caso.',
            'Se ha escalado el reporte a nivel de supervisión.',
            'Trabajo completado. Esperando verificación del ciudadano.',
            'Se requiere más información del ciudadano para proceder.',
            'El técnico reporta dificultades de acceso a la zona.',
            'Reporte cerrado exitosamente tras confirmación del ciudadano.',
        ];

        // Obtener IDs de reportes y usuarios
        $reportes = DB::table('reportes')->pluck('id')->toArray();
        $usuarios = DB::table('users')->pluck('id')->toArray();

        $updates = [];
        $batchSize = 100;

        for ($i = 0; $i < 500; $i++) {
            $reporteId = $reportes[array_rand($reportes)];
            $tipo = $tipos[array_rand($tipos)];
            $userId = $usuarios[array_rand($usuarios)];

            // Obtener el reporte para sacar su fecha de creación
            $reporte = DB::table('reportes')->where('id', $reporteId)->first();
            $createdAt = Carbon::parse($reporte->created_at)->addHours(rand(1, 72))->addMinutes(rand(0, 59));

            $contenido = null;
            $archivoUrl = null;
            $estadoAnterior = null;
            $estadoNuevo = null;

            switch ($tipo) {
                case 'comentario':
                    $contenido = $contenidos[array_rand($contenidos)];
                    break;

                case 'cambio_estado':
                    $estadoAnterior = $estados[array_rand($estados)];
                    do {
                        $estadoNuevo = $estados[array_rand($estados)];
                    } while ($estadoAnterior === $estadoNuevo);
                    $contenido = "Estado cambiado de {$estadoAnterior} a {$estadoNuevo}";
                    break;

                case 'imagen':
                    $contenido = 'Imagen adjunta del progreso de la solución';
                    $archivoUrl = 'https://via.placeholder.com/640x480.png?text=Update+' . ($i + 1);
                    break;

                case 'asignacion':
                    $contenido = "Reporte asignado al operador";
                    break;

                case 'reasignacion':
                    $contenido = "Reporte reasignado a otro operador por carga de trabajo";
                    break;

                case 'sistema':
                    $contenido = "Actualización automática del sistema";
                    $userId = null;
                    break;
            }

            $updates[] = [
                'reporte_id' => $reporteId,
                'user_id' => $userId,
                'tipo' => $tipo,
                'contenido' => $contenido,
                'archivo_url' => $archivoUrl,
                'visible_ciudadano' => rand(0, 1) ? true : ($tipo === 'sistema' ? false : true),
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Insertar en lotes
            if (count($updates) >= $batchSize) {
                DB::table('reporte_updates')->insert($updates);
                $updates = [];
            }
        }

        // Insertar registros restantes
        if (!empty($updates)) {
            DB::table('reporte_updates')->insert($updates);
        }

        $this->command->info('500 actualizaciones de reportes insertadas correctamente.');
    }
}
