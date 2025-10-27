<?php

namespace App\Jobs;

use App\Models\Reporte;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectDuplicatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 30;

    protected $reporte;

    /**
     * Create a new job instance.
     */
    public function __construct(Reporte $reporte)
    {
        $this->reporte = $reporte;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Detectar si hay reportes similares
        $reporteSimilar = $this->reporte->detectarDuplicados();

        if ($reporteSimilar) {
            \Log::info('Reporte similar detectado', [
                'reporte_nuevo' => $this->reporte->id,
                'reporte_similar' => $reporteSimilar->id
            ]);

            // Marcar como duplicado automáticamente
            $this->reporte->marcarComoDuplicado($reporteSimilar->id);

            // Crear update en el reporte padre
            $reporteSimilar->agregarComentario(
                "Se detectó un reporte duplicado (ID: {$this->reporte->id}). Total de afectados: " .
                ($reporteSimilar->duplicados_count + 1),
                true
            );
        }

        // Recalcular prioridad basada en duplicados y zona
        $this->reporte->calcularPrioridad();
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Error detectando duplicados', [
            'reporte_id' => $this->reporte->id,
            'error' => $exception->getMessage()
        ]);
    }
}
