<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportesSeeder extends Seeder
{
    public function run(): void
    {
        // Usar IDs de estados de la tabla normalizada
        $estadosIds = DB::table('estados')->where('activo', true)->pluck('id')->toArray();
        $prioridades = ['baja', 'media', 'alta', 'critica'];

        $nombres = ['Juan Pérez', 'María González', 'Carlos Rodríguez', 'Ana Martínez', 'Luis Hernández', 'Carmen López', 'José García', 'Laura Sánchez', 'Miguel Torres', 'Patricia Ramírez'];
        $barrios = ['Centro', 'Norte', 'Sur', 'El Poblado', 'Laureles', 'Belen', 'Castilla', 'La América', 'Villa Hermosa', 'Buenos Aires'];
        $localidades = ['Comuna 1', 'Comuna 2', 'Comuna 3', 'Comuna 4', 'Comuna 5', 'Comuna 6', 'Comuna 7', 'Comuna 8', 'Comuna 9', 'Comuna 10'];

        $descripciones = [
            'Llevo 3 días sin servicio de energía eléctrica en mi sector. Necesito una solución urgente.',
            'La conexión de internet es muy lenta e intermitente desde hace una semana.',
            'Hay una fuga de gas en mi edificio. Necesito atención inmediata.',
            'El suministro de agua ha sido suspendido sin previo aviso.',
            'Daño en el alumbrado público de toda la cuadra desde hace varios días.',
            'Problemas con la presión del agua en horas de la mañana.',
            'Cortes frecuentes del servicio eléctrico afectando electrodomésticos.',
            'Internet cae constantemente especialmente en las noches.',
            'Medidor de gas presenta fallas y marca lecturas incorrectas.',
            'No hay agua desde el fin de semana pasado.',
        ];

        // Obtener IDs de servicios, ciudades, proveedores y usuarios
        $servicios = DB::table('servicios')->pluck('id')->toArray();
        $ciudades = DB::table('ciudades')->pluck('id')->toArray();
        $proveedores = DB::table('proveedores')->pluck('id')->toArray();
        $usuarios = DB::table('users')->pluck('id')->toArray();

        $reportes = [];
        $batchSize = 100;

        for ($i = 0; $i < 500; $i++) {
            $createdAt = Carbon::now()->subDays(rand(0, 365))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $estadoId = $estadosIds[array_rand($estadosIds)];
            $prioridad = $prioridades[array_rand($prioridades)];

            $assignedTo = null;
            $assignedAt = null;
            $deadline = null;

            // Si el estado no es "pendiente" (id=1), asignar operador
            if ($estadoId > 1) {
                $assignedTo = $usuarios[array_rand($usuarios)];
                $assignedAt = (clone $createdAt)->addHours(rand(1, 48));
                $deadline = (clone $assignedAt)->addHours(rand(24, 168));
            }

            $imagenes = [];
            if (rand(0, 1)) {
                $numImagenes = rand(1, 3);
                for ($j = 0; $j < $numImagenes; $j++) {
                    $imagenes[] = 'https://via.placeholder.com/640x480.png?text=Reporte+' . ($i + 1) . '+Imagen+' . ($j + 1);
                }
            }

            $reportes[] = [
                'nombres' => $nombres[array_rand($nombres)],
                'correo' => 'ciudadano' . ($i + 1) . '@example.com',
                'telefono' => '300' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'servicio_id' => $servicios[array_rand($servicios)],
                'ciudad_id' => $ciudades[array_rand($ciudades)],
                'proveedor_id' => $proveedores[array_rand($proveedores)],
                'descripcion' => $descripciones[array_rand($descripciones)],
                'estado_id' => $estadoId,
                'direccion' => 'Calle ' . rand(1, 100) . ' # ' . rand(1, 50) . '-' . rand(1, 99),
                'localidad' => $localidades[array_rand($localidades)],
                'barrio' => $barrios[array_rand($barrios)],
                'latitude' => 6.2 + (rand(-500, 500) / 1000),
                'longitude' => -75.5 + (rand(-500, 500) / 1000),
                'prioridad' => $prioridad,
                'assigned_to' => $assignedTo,
                'assigned_at' => $assignedAt,
                'deadline' => $deadline,
                'sla_hours' => rand(24, 168),
                'parent_id' => null,
                'duplicados_count' => 0,
                'imagenes' => !empty($imagenes) ? json_encode($imagenes) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Insertar en lotes
            if (count($reportes) >= $batchSize) {
                DB::table('reportes')->insert($reportes);
                $reportes = [];
            }
        }

        // Insertar registros restantes
        if (!empty($reportes)) {
            DB::table('reportes')->insert($reportes);
        }

        $this->command->info('500 reportes insertados correctamente.');
    }
}
