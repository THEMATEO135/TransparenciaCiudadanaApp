<?php

namespace App\Jobs;

use App\Models\Prediccion;
use App\Models\Reporte;
use App\Services\PredictionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPredictionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 600; // 10 minutos

    /**
     * Execute the job.
     */
    public function handle(PredictionService $predictionService): void
    {
        \Log::info('Iniciando proceso de predicciones');

        // Generar predicciones para las próximas 72 horas
        $predicciones = $predictionService->generarPredicciones(72);

        \Log::info('Predicciones generadas', ['count' => $predicciones->count()]);

        // Enviar alertas para predicciones de alta probabilidad
        $predictionService->enviarAlertas();
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Error procesando predicciones', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
