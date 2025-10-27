<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReporteEstadistico;
use Carbon\Carbon;

class ReportesEstadisticosSeeder extends Seeder
{
    public function run(): void
    {
        $reportes = [
            [
                'nombre' => 'Reporte Mensual de Incidencias',
                'frecuencia' => 'mensual',
                'configuracion' => [
                    'incluir_graficos' => true,
                    'incluir_comparativa' => true,
                    'filtros' => []
                ],
                'destinatarios' => [
                    'admin@transparencia.com',
                    'supervisor@transparencia.com'
                ],
                'activo' => true,
                'proxima_ejecucion' => Carbon::now()->addMonth()->startOfMonth()->addHours(8),
            ],
            [
                'nombre' => 'Reporte Semanal por Servicio',
                'frecuencia' => 'semanal',
                'configuracion' => [
                    'incluir_graficos' => true,
                    'agrupar_por' => 'servicio',
                    'filtros' => []
                ],
                'destinatarios' => [
                    'admin@transparencia.com'
                ],
                'activo' => true,
                'proxima_ejecucion' => Carbon::now()->next(Carbon::MONDAY)->addHours(8),
            ],
            [
                'nombre' => 'Reporte Diario de Reportes Críticos',
                'frecuencia' => 'diario',
                'configuracion' => [
                    'filtros' => [
                        'prioridad' => 'critica'
                    ]
                ],
                'destinatarios' => [
                    'admin@transparencia.com',
                    'supervisor@transparencia.com',
                    'operador1@transparencia.com'
                ],
                'activo' => true,
                'proxima_ejecucion' => Carbon::tomorrow()->addHours(8),
            ],
        ];

        foreach ($reportes as $reporte) {
            ReporteEstadistico::create($reporte);
        }

        $this->command->info('✅ Reportes estadísticos programados creados.');
    }
}
