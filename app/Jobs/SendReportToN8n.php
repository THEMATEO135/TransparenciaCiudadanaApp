<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendReportToN8n implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $eventData;
    protected $eventType;

    public $tries = 3; // Número de reintentos
    public $timeout = 30; // Timeout en segundos

    /**
     * @param array $eventData Datos del evento
     * @param string $eventType Tipo de evento: 'reporte_nuevo', 'actualizacion_estado', 'comentario', 'feedback'
     */
    public function __construct(array $eventData, string $eventType = 'reporte_nuevo')
    {
        $this->eventData = $eventData;
        $this->eventType = $eventType;
    }

    public function handle()
    {
        $webhookUrl = env('WEBHOOK_URL');

        if (empty($webhookUrl)) {
            Log::warning('WEBHOOK_URL no está configurada en el .env');
            return;
        }

        try {
            // Agregar metadatos del evento
            $payload = array_merge($this->eventData, [
                'event_type' => $this->eventType,
                'timestamp' => now()->toISOString(),
                'source' => 'laravel_app',
                'app_name' => config('app.name'),
            ]);

            Log::info("Enviando webhook a n8n", [
                'event_type' => $this->eventType,
                'url' => $webhookUrl,
                'payload_size' => strlen(json_encode($payload))
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'Laravel-Webhook-Client/1.0',
                'X-Webhook-Source' => 'laravel-transparencia',
                'X-Event-Type' => $this->eventType,
                'X-Request-ID' => uniqid('webhook_', true)
            ])
            ->timeout($this->timeout)
            ->connectTimeout(10)
            ->post($webhookUrl, $payload);

            if ($response->successful()) {
                Log::info('Webhook enviado exitosamente a n8n', [
                    'event_type' => $this->eventType,
                    'status' => $response->status(),
                    'response_body' => $response->body()
                ]);
            } else {
                throw new \Exception("Error HTTP {$response->status()}: {$response->body()}");
            }

        } catch (\Exception $e) {
            Log::error('Error enviando webhook a n8n: ' . $e->getMessage(), [
                'event_type' => $this->eventType,
                'attempt' => $this->attempts(),
                'trace' => $e->getTraceAsString()
            ]);

            // Relanzar la excepción para que Laravel reintente automáticamente
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Job SendReportToN8n falló después de todos los reintentos', [
            'event_type' => $this->eventType,
            'error' => $exception->getMessage(),
            'data' => $this->eventData
        ]);
    }
}