<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReporteEstadistico;
use App\Jobs\GenerateReporteEstadisticoJob;

class EjecutarReportesEstadisticosCommand extends Command
{
    protected $signature = 'reportes-estadisticos:ejecutar';
    protected $description = 'Ejecuta los reportes estadísticos programados pendientes';

    public function handle()
    {
        $this->info('Buscando reportes estadísticos pendientes...');

        $reportes = ReporteEstadistico::pendientes()->get();

        if ($reportes->isEmpty()) {
            $this->comment('No hay reportes pendientes de ejecución.');
            return Command::SUCCESS;
        }

        $this->info("Reportes a generar: {$reportes->count()}");

        foreach ($reportes as $reporte) {
            $this->line("- {$reporte->nombre} (Frecuencia: {$reporte->frecuencia})");
            GenerateReporteEstadisticoJob::dispatch($reporte);
        }

        $this->info('✅ Jobs despachados a la cola.');

        return Command::SUCCESS;
    }
}
