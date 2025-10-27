<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlantillaRespuesta;

class PlantillasRespuestaSeeder extends Seeder
{
    public function run(): void
    {
        $plantillas = [
            [
                'nombre' => 'Reporte Recibido',
                'asunto' => 'Hemos recibido tu reporte #{id_reporte}',
                'tipo' => 'informacion',
                'contenido' => 'Hola {nombre_ciudadano},

Hemos recibido tu reporte sobre {servicio} en {barrio}, {ciudad}.

**Detalles del reporte:**
- ID: #{id_reporte}
- Servicio: {servicio}
- Ubicación: {direccion}
- Fecha: {fecha_reporte}

Nuestro equipo está evaluando tu caso y te notificaremos cuando haya novedades.

Gracias por tu reporte.',
                'activa' => true,
            ],
            [
                'nombre' => 'Asignado a Operador',
                'asunto' => 'Tu reporte ha sido asignado',
                'tipo' => 'informacion',
                'contenido' => 'Hola {nombre_ciudadano},

Tu reporte #{id_reporte} ha sido asignado a uno de nuestros operadores.

**Estado actual:** Asignado
**Tiempo estimado de resolución:** {fecha_estimada}

Te mantendremos informado del progreso.

Saludos,
Equipo de Transparencia Ciudadana',
                'activa' => true,
            ],
            [
                'nombre' => 'Mantenimiento Programado',
                'asunto' => 'Mantenimiento programado en tu zona',
                'tipo' => 'mantenimiento',
                'contenido' => 'Hola {nombre_ciudadano},

Te informamos que hay un mantenimiento programado en tu zona ({barrio}, {localidad}) para el servicio de {servicio}.

**Fecha estimada:** {fecha_estimada}
**Proveedor:** {proveedor}

El problema reportado en tu reporte #{id_reporte} será atendido durante este mantenimiento.

Lamentamos las molestias.',
                'activa' => true,
            ],
            [
                'nombre' => 'Requiere Más Información',
                'asunto' => 'Necesitamos más información sobre tu reporte',
                'tipo' => 'otro',
                'contenido' => 'Hola {nombre_ciudadano},

Para poder resolver tu reporte #{id_reporte} sobre {servicio}, necesitamos información adicional.

Por favor, responde a este correo proporcionando:
- Horario específico del problema
- Imágenes o evidencia adicional
- Cualquier detalle que consideres relevante

Tu colaboración es muy importante.

Gracias,
Equipo de Transparencia',
                'activa' => true,
            ],
            [
                'nombre' => 'Problema Resuelto',
                'asunto' => '¡Tu reporte ha sido resuelto!',
                'tipo' => 'resolucion',
                'contenido' => 'Hola {nombre_ciudadano},

¡Buenas noticias! Tu reporte #{id_reporte} sobre {servicio} ha sido marcado como resuelto.

**Detalles:**
- Servicio: {servicio}
- Ubicación: {direccion}
- Fecha de reporte: {fecha_reporte}
- Fecha de resolución: {fecha_estimada}

Por favor, confirma que el problema se ha solucionado haciendo clic en el siguiente enlace:
[Enlace de confirmación]

Tu opinión es muy valiosa para nosotros.

¡Gracias por usar Transparencia Ciudadana!',
                'activa' => true,
            ],
            [
                'nombre' => 'Escalado a Proveedor',
                'asunto' => 'Tu reporte ha sido escalado',
                'tipo' => 'escalado',
                'contenido' => 'Hola {nombre_ciudadano},

Tu reporte #{id_reporte} ha sido escalado directamente al proveedor {proveedor}.

Esto significa que el caso requiere atención directa del proveedor del servicio. Estamos haciendo seguimiento para garantizar una pronta solución.

**Tiempo estimado:** {fecha_estimada}

Te notificaremos cuando tengamos novedades.

Gracias por tu paciencia.',
                'activa' => true,
            ],
            [
                'nombre' => 'Reporte Duplicado',
                'asunto' => 'Tu reporte se unió a un caso existente',
                'tipo' => 'informacion',
                'contenido' => 'Hola {nombre_ciudadano},

Tu reporte #{id_reporte} ha sido identificado como parte de un problema más amplio que ya estamos atendiendo.

**Caso principal:** Se han reportado múltiples incidencias similares en {barrio}.
**Prioridad:** ALTA debido al número de afectados.

Al unificar los reportes, podemos dar una solución más rápida y efectiva.

Te mantendremos informado del progreso.',
                'activa' => true,
            ],
            [
                'nombre' => 'En Proceso de Resolución',
                'asunto' => 'Estamos trabajando en tu reporte',
                'tipo' => 'informacion',
                'contenido' => 'Hola {nombre_ciudadano},

Tu reporte #{id_reporte} está actualmente en proceso de resolución.

**Estado:** {estado}
**Prioridad:** {descripcion}
**Tiempo estimado de resolución:** {fecha_estimada}

Nuestro equipo está trabajando activamente en solucionar el problema.

Gracias por tu paciencia.',
                'activa' => true,
            ],
        ];

        foreach ($plantillas as $plantilla) {
            PlantillaRespuesta::create($plantilla);
        }

        $this->command->info('✅ Plantillas de respuesta creadas exitosamente.');
    }
}
