<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReporteFeedbacksSeeder extends Seeder
{
    public function run(): void
    {
        $comentarios = [
            'Excelente servicio, resolvieron el problema muy rápido.',
            'El técnico fue muy profesional y resolvió el inconveniente.',
            'Tardaron un poco pero finalmente solucionaron el problema.',
            'Muy satisfecho con la atención recibida.',
            'El servicio mejoró notablemente después de la reparación.',
            'Aún persiste el problema, no quedó totalmente resuelto.',
            'Muy buena comunicación durante todo el proceso.',
            'Esperaba una solución más rápida.',
            'El técnico explicó claramente el problema y la solución.',
            'Todo funcionando perfectamente ahora, gracias.',
            null, // Algunos feedbacks sin comentario
        ];

        // Obtener IDs de reportes que están en estado resuelto o cerrado Y que NO tienen feedback
        $reportes = DB::table('reportes')
            ->whereIn('estado', ['resuelto', 'cerrado'])
            ->whereNotIn('id', DB::table('reporte_feedbacks')->pluck('reporte_id'))
            ->pluck('id')
            ->toArray();

        if (empty($reportes)) {
            $this->command->warn('No hay reportes resueltos o cerrados para crear feedbacks.');
            return;
        }

        $feedbacks = [];
        $batchSize = 100;
        $reportesUsados = [];

        for ($i = 0; $i < 500; $i++) {
            // Asegurar que no usamos el mismo reporte dos veces (unique constraint)
            do {
                $reporteId = $reportes[array_rand($reportes)];
            } while (in_array($reporteId, $reportesUsados) && count($reportesUsados) < count($reportes));

            if (count($reportesUsados) >= count($reportes)) {
                $this->command->warn('No hay suficientes reportes únicos resueltos/cerrados. Se crearon ' . count($feedbacks) . ' feedbacks.');
                break;
            }

            $reportesUsados[] = $reporteId;

            $reporte = DB::table('reportes')->where('id', $reporteId)->first();
            $respondidoAt = Carbon::parse($reporte->updated_at)->addHours(rand(1, 48));

            // 80% de feedbacks respondidos, 20% pendientes
            $respondido = rand(0, 100) < 80;

            $feedbacks[] = [
                'reporte_id' => $reporteId,
                'resuelto' => $respondido ? (rand(0, 100) < 85 ? true : false) : null, // 85% confirman resolución
                'calificacion' => $respondido ? rand(1, 5) : null,
                'nps' => $respondido ? rand(0, 10) : null,
                'comentario' => $respondido ? $comentarios[array_rand($comentarios)] : null,
                'respondido_at' => $respondido ? $respondidoAt : null,
                'token' => Str::random(32),
                'created_at' => Carbon::parse($reporte->updated_at)->addMinutes(rand(5, 60)),
                'updated_at' => $respondido ? $respondidoAt : Carbon::parse($reporte->updated_at)->addMinutes(rand(5, 60)),
            ];

            // Insertar en lotes
            if (count($feedbacks) >= $batchSize) {
                DB::table('reporte_feedbacks')->insert($feedbacks);
                $feedbacks = [];
            }
        }

        // Insertar registros restantes
        if (!empty($feedbacks)) {
            DB::table('reporte_feedbacks')->insert($feedbacks);
        }

        $this->command->info(count($reportesUsados) . ' feedbacks de reportes insertados correctamente.');
    }
}
