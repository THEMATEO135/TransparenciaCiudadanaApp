<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlantillasRespuestaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = ['resolucion', 'informacion', 'mantenimiento', 'escalado', 'otro'];

        $plantillasBase = [
            [
                'tipo' => 'resolucion',
                'asuntos' => [
                    'Resolución de Incidencia - Servicio Restaurado',
                    'Confirmación de Solución - Caso Cerrado',
                    'Finalización de Trabajo - Servicio Restablecido',
                    'Reporte Solucionado Exitosamente',
                    'Cierre de Caso - Trabajo Completado',
                ],
                'contenidos' => [
                    'Estimado/a {nombre_ciudadano}, nos complace informarle que el problema reportado en {barrio} ha sido resuelto. Nuestro equipo técnico completó las reparaciones necesarias el día de hoy. Agradecemos su paciencia.',
                    'Hola {nombre_ciudadano}, le confirmamos que la incidencia en su sector ha sido solucionada. El servicio fue restablecido completamente. Si experimenta algún inconveniente, no dude en contactarnos.',
                    'Apreciado ciudadano, el trabajo de reparación en {barrio} ha finalizado exitosamente. El servicio debería estar funcionando con normalidad. Fecha estimada de finalización: {fecha_estimada}.',
                    'Estimado usuario, nos es grato comunicarle que hemos resuelto el inconveniente reportado. Nuestros técnicos verificaron el correcto funcionamiento del servicio en su zona.',
                    'Buen día {nombre_ciudadano}, el caso reportado ha sido cerrado satisfactoriamente. Todas las reparaciones fueron completadas según lo programado.',
                ],
            ],
            [
                'tipo' => 'informacion',
                'asuntos' => [
                    'Actualización de Estado - Reporte en Proceso',
                    'Información sobre su Reporte',
                    'Estado Actual de su Solicitud',
                    'Seguimiento a su Caso',
                    'Notificación de Progreso',
                ],
                'contenidos' => [
                    'Estimado/a {nombre_ciudadano}, le informamos que su reporte está siendo atendido. Nuestro equipo está trabajando en la solución. Tiempo estimado: {fecha_estimada}.',
                    'Hola, queremos mantenerlo informado sobre el progreso de su caso en {barrio}. Actualmente estamos en la fase de diagnóstico y pronto procederemos con las reparaciones.',
                    'Apreciado ciudadano, su reporte ha sido asignado a nuestro equipo técnico especializado. Estamos evaluando la situación para brindarle la mejor solución posible.',
                    'Le notificamos que estamos trabajando activamente en su caso. Hemos identificado la causa del problema y estamos coordinando las acciones necesarias.',
                    'Estimado usuario, su solicitud está en proceso. Nuestros técnicos visitarán la zona en las próximas horas para realizar las verificaciones correspondientes.',
                ],
            ],
            [
                'tipo' => 'mantenimiento',
                'asuntos' => [
                    'Mantenimiento Programado en su Sector',
                    'Aviso de Mantenimiento Preventivo',
                    'Trabajo de Mantenimiento Programado',
                    'Notificación de Mantenimiento en {barrio}',
                    'Programación de Mantenimiento - Suspensión Temporal',
                ],
                'contenidos' => [
                    'Estimado/a {nombre_ciudadano}, le informamos que realizaremos mantenimiento preventivo en {barrio} el día {fecha_estimada}. El servicio podría verse interrumpido temporalmente.',
                    'Apreciado ciudadano, hemos programado trabajos de mantenimiento en su zona. Esto podría ocasionar una suspensión breve del servicio. Agradecemos su comprensión.',
                    'Le notificamos sobre un mantenimiento programado en la infraestructura de su sector. Fecha: {fecha_estimada}. Duración estimada: 4 horas.',
                    'Estimado usuario, realizaremos mejoras en la red de su zona. Durante este tiempo, el servicio estará temporalmente suspendido. Disculpe las molestias.',
                    'Hola {nombre_ciudadano}, como parte de nuestro plan de mantenimiento preventivo, estaremos trabajando en {barrio}. El servicio será restablecido lo antes posible.',
                ],
            ],
            [
                'tipo' => 'escalado',
                'asuntos' => [
                    'Escalamiento de Caso - Atención Prioritaria',
                    'Su Caso ha sido Escalado',
                    'Priorización de su Reporte',
                    'Atención Especializada - Caso Escalado',
                    'Seguimiento Prioritario',
                ],
                'contenidos' => [
                    'Estimado/a {nombre_ciudadano}, su caso ha sido escalado a nuestro equipo de atención especializada debido a su complejidad. Estamos trabajando con prioridad en la solución.',
                    'Le informamos que su reporte en {barrio} requiere atención de nivel superior. Hemos asignado recursos adicionales para resolver el problema lo antes posible.',
                    'Apreciado ciudadano, su caso ha sido priorizado. Un supervisor está coordinando directamente las acciones necesarias para brindarle una solución efectiva.',
                    'Su reporte ha sido escalado a la gerencia técnica para una atención más especializada. Estamos comprometidos en resolver su inconveniente.',
                    'Estimado usuario, debido a la naturaleza del problema, hemos involucrado a nuestro equipo de expertos. Recibirá actualizaciones constantes sobre el progreso.',
                ],
            ],
            [
                'tipo' => 'otro',
                'asuntos' => [
                    'Información Adicional Requerida',
                    'Solicitud de Detalles Complementarios',
                    'Necesitamos más Información',
                    'Datos Adicionales para su Caso',
                    'Complemento de Información',
                ],
                'contenidos' => [
                    'Estimado/a {nombre_ciudadano}, para proceder con su caso necesitamos información adicional. Por favor contáctenos al número de atención al cliente.',
                    'Hola, para darle un mejor servicio, requerimos que nos proporcione algunos detalles adicionales sobre el problema en {barrio}.',
                    'Apreciado ciudadano, hemos revisado su reporte y necesitamos que nos confirme algunos datos para continuar con el proceso de solución.',
                    'Le solicitamos amablemente que nos brinde información complementaria sobre la incidencia reportada. Esto nos ayudará a resolver más eficientemente.',
                    'Estimado usuario, para optimizar la atención de su caso, necesitamos que nos proporcione fotografías o descripciones adicionales del problema.',
                ],
            ],
        ];

        $plantillas = [];
        $batchSize = 100;
        $totalInsertado = 0;

        // Generar 500 plantillas
        for ($i = 0; $i < 500; $i++) {
            $tipoData = $plantillasBase[array_rand($plantillasBase)];
            $tipo = $tipoData['tipo'];
            $asunto = $tipoData['asuntos'][array_rand($tipoData['asuntos'])];
            $contenido = $tipoData['contenidos'][array_rand($tipoData['contenidos'])];

            $plantillas[] = [
                'nombre' => 'Plantilla ' . $tipo . ' #' . ($i + 1),
                'asunto' => $asunto,
                'contenido' => $contenido,
                'tipo' => $tipo,
                'activa' => rand(0, 10) > 2, // 80% activas
                'uso_count' => rand(0, 100),
                'created_at' => Carbon::now()->subDays(rand(0, 180)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30)),
            ];

            // Insertar en lotes
            if (count($plantillas) >= $batchSize) {
                DB::table('plantillas_respuesta')->insert($plantillas);
                $totalInsertado += count($plantillas);
                $plantillas = [];
            }
        }

        // Insertar registros restantes
        if (!empty($plantillas)) {
            DB::table('plantillas_respuesta')->insert($plantillas);
        }

        $this->command->info('500 plantillas de respuesta insertadas correctamente.');
    }
}
