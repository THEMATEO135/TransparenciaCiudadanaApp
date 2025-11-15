<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = ['info', 'warning', 'danger', 'success'];

        $notificacionesData = [
            'info' => [
                'titulos' => [
                    'Nuevo Reporte Asignado',
                    'Actualización de Sistema',
                    'Información Importante',
                    'Recordatorio de Tareas',
                    'Nuevo Mensaje',
                ],
                'mensajes' => [
                    'Se te ha asignado un nuevo reporte para revisar.',
                    'El sistema será actualizado esta noche.',
                    'Hay nuevas plantillas de respuesta disponibles.',
                    'Tienes reportes pendientes de revisión.',
                    'Un ciudadano ha enviado información adicional sobre un reporte.',
                ],
                'links' => [
                    '/admin/reportes',
                    '/admin/dashboard',
                    '/admin/plantillas',
                    '/admin/reportes/pendientes',
                    '/admin/mensajes',
                ],
            ],
            'warning' => [
                'titulos' => [
                    'Reporte con Retraso',
                    'SLA Próximo a Vencer',
                    'Atención Requerida',
                    'Prioridad Alta Pendiente',
                    'Verificación Necesaria',
                ],
                'mensajes' => [
                    'Un reporte asignado supera el tiempo estimado de resolución.',
                    'El SLA de un reporte vence en las próximas 4 horas.',
                    'Un reporte requiere tu atención inmediata.',
                    'Hay casos de alta prioridad sin asignar.',
                    'Debes verificar la resolución de un reporte.',
                ],
                'links' => [
                    '/admin/reportes/atrasados',
                    '/admin/reportes/sla',
                    '/admin/reportes/urgentes',
                    '/admin/reportes/alta-prioridad',
                    '/admin/reportes/verificacion',
                ],
            ],
            'danger' => [
                'titulos' => [
                    'SLA Vencido',
                    'Reporte Crítico',
                    'Escalamiento de Caso',
                    'Múltiples Reportes en Zona',
                    'Error en el Sistema',
                ],
                'mensajes' => [
                    'Un reporte ha excedido el tiempo máximo de resolución.',
                    'Nuevo reporte con prioridad CRÍTICA requiere atención inmediata.',
                    'Un caso ha sido escalado a nivel superior.',
                    'Se detectaron múltiples reportes en la misma zona.',
                    'Se ha detectado un error en el procesamiento de reportes.',
                ],
                'links' => [
                    '/admin/reportes/vencidos',
                    '/admin/reportes/criticos',
                    '/admin/reportes/escalados',
                    '/admin/reportes/zona-afectada',
                    '/admin/sistema/errores',
                ],
            ],
            'success' => [
                'titulos' => [
                    'Reporte Resuelto',
                    'Feedback Positivo',
                    'Meta Alcanzada',
                    'Confirmación del Ciudadano',
                    'Tarea Completada',
                ],
                'mensajes' => [
                    'Un reporte ha sido marcado como resuelto exitosamente.',
                    'Has recibido una calificación de 5 estrellas de un ciudadano.',
                    'Has alcanzado tu meta de resolución mensual.',
                    'El ciudadano confirmó la resolución del problema.',
                    'Has completado todas tus tareas pendientes del día.',
                ],
                'links' => [
                    '/admin/reportes/resueltos',
                    '/admin/feedback',
                    '/admin/estadisticas',
                    '/admin/reportes/confirmados',
                    '/admin/tareas',
                ],
            ],
        ];

        // Obtener IDs de usuarios
        $usuarios = DB::table('users')->pluck('id')->toArray();

        if (empty($usuarios)) {
            $this->command->warn('No hay usuarios para crear notificaciones.');
            return;
        }

        $notificaciones = [];
        $batchSize = 100;

        for ($i = 0; $i < 500; $i++) {
            $userId = $usuarios[array_rand($usuarios)];
            $tipo = $tipos[array_rand($tipos)];

            $data = $notificacionesData[$tipo];
            $titulo = $data['titulos'][array_rand($data['titulos'])];
            $mensaje = $data['mensajes'][array_rand($data['mensajes'])];
            $link = $data['links'][array_rand($data['links'])];

            $createdAt = Carbon::now()->subDays(rand(0, 60))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            // 60% de notificaciones leídas
            $leida = rand(0, 100) < 60;
            $readAt = $leida ? (clone $createdAt)->addHours(rand(1, 72)) : null;

            $notificaciones[] = [
                'user_id' => $userId,
                'type' => $tipo,
                'title' => $titulo,
                'message' => $mensaje,
                'link' => $link,
                'read' => $leida,
                'read_at' => $readAt,
                'created_at' => $createdAt,
                'updated_at' => $readAt ?? $createdAt,
            ];

            // Insertar en lotes
            if (count($notificaciones) >= $batchSize) {
                DB::table('notifications')->insert($notificaciones);
                $notificaciones = [];
            }
        }

        // Insertar registros restantes
        if (!empty($notificaciones)) {
            DB::table('notifications')->insert($notificaciones);
        }

        $this->command->info('500 notificaciones insertadas correctamente.');
    }
}
