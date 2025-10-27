<?php

namespace App\Jobs;

use App\Models\ReporteEstadistico;
use App\Services\ReporteEstadisticoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateReporteEstadisticoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 600;

    protected $reporteEstadistico;

    /**
     * Create a new job instance.
     */
    public function __construct(ReporteEstadistico $reporteEstadistico)
    {
        $this->reporteEstadistico = $reporteEstadistico;
    }

    /**
     * Execute the job.
     */
    public function handle(ReporteEstadisticoService $service): void
    {
        \Log::info('Generando reporte estadístico', [
            'id' => $this->reporteEstadistico->id,
            'nombre' => $this->reporteEstadistico->nombre
        ]);

        $service->generar($this->reporteEstadistico);

        $this->reporteEstadistico->marcarEjecutado();

        \Log::info('Reporte estadístico generado y enviado', [
            'id' => $this->reporteEstadistico->id
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Error generando reporte estadístico', [
            'reporte_estadistico_id' => $this->reporteEstadistico->id,
            'error' => $exception->getMessage()
        ]);
    }
}
