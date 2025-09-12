<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Reporte;
use App\Models\Servicio;
use Illuminate\Validation\ValidationException;

class ReporteController extends Controller
{
    public function index() 
    {
        return view('reportes.crear');
    }

    public function store(Request $request)
    {
        try {
            // Log para debugging
            Log::info('Datos recibidos en ReporteController::store', $request->all());

            $validated = $request->validate([
                'nombres' => 'required|string|max:255',
                'correo' => 'required|email|max:255',
                'telefono' => 'required|string|max:20',
                'servicio_id' => 'required|integer|exists:servicios,id',
                'descripcion' => 'required|string|max:1000',
                'direccion' => 'nullable|string|max:255',
                'localidad' => 'nullable|string|max:100',
                'barrio' => 'nullable|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            // Crear el reporte
            $reporte = Reporte::create($validated);
            Log::info('Reporte creado exitosamente', ['id' => $reporte->id]);

            // Preparar payload con datos adicionales
            $payload = array_merge($reporte->toArray(), [
                'created_at_formatted' => $reporte->created_at->format('Y-m-d H:i:s'),
                'servicio' => $reporte->servicio ? $reporte->servicio->toArray() : null,
                'timestamp' => now()->timestamp,
                'source' => 'laravel_app'
            ]);
            
            Log::info('Payload preparado para webhook', $payload);

            // Enviar webhook
            $this->sendWebhook($payload);

            return response()->json([
                'ok' => true,
                'message' => 'Reporte enviado correctamente',
                'id' => $reporte->id
            ]);

        } catch (ValidationException $e) {
            Log::warning('Error de validación', $e->errors());
            
            return response()->json([
                'ok' => false,
                'error' => 'Datos de entrada inválidos',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error general en store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'ok' => false,
                'error' => 'Error interno del servidor',
                'message' => config('app.debug') ? $e->getMessage() : 'Por favor intenta nuevamente'
            ], 500);
        }
    }

    /**
     * Envía el webhook con múltiples intentos y mejor manejo de errores
     */
    private function sendWebhook($payload)
    {
        // URL del webhook desde el .env
        $webhookUrl = env('WEBHOOK_URL', 'https://primary-production-c6f0f.up.railway.app/webhook/transparencia_webhook');
        
        if (empty($webhookUrl)) {
            Log::warning('WEBHOOK_URL no está configurada en el .env');
            return;
        }

        Log::info('Enviando webhook', [
            'url' => $webhookUrl,
            'payload_size' => strlen(json_encode($payload))
        ]);

        $maxAttempts = 3;
        $attempt = 1;

        while ($attempt <= $maxAttempts) {
            try {
                Log::info("Intento #{$attempt} de envío de webhook");

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'Laravel-Webhook-Client/1.0',
                    'X-Webhook-Source' => 'laravel-transparencia',
                    'X-Request-ID' => uniqid('webhook_', true)
                ])
                ->timeout(30)  // 30 segundos de timeout
                ->connectTimeout(10)  // 10 segundos para conectar
                ->post($webhookUrl, $payload);

                // Log detallado de la respuesta
                Log::info("Respuesta del webhook (intento #{$attempt})", [
                    'status_code' => $response->status(),
                    'headers' => $response->headers(),
                    'body' => $response->body(),
                    'successful' => $response->successful(),
                    'client_error' => $response->clientError(),
                    'server_error' => $response->serverError()
                ]);

                if ($response->successful()) {
                    Log::info('Webhook enviado exitosamente', [
                        'attempt' => $attempt,
                        'status' => $response->status(),
                        'response_body' => $response->body()
                    ]);
                    return; // Éxito, salir del bucle
                }

                // Si es error 4xx, no reintentar
                if ($response->clientError()) {
                    Log::error('Error del cliente en webhook (4xx) - no reintentando', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    return;
                }

                // Error 5xx o de conectividad, reintentar
                Log::warning("Error en intento #{$attempt}, reintentando...", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("Error de conexión en intento #{$attempt}: " . $e->getMessage());
                
            } catch (\Illuminate\Http\Client\RequestException $e) {
                Log::error("Error de request en intento #{$attempt}: " . $e->getMessage());
                
            } catch (\Exception $e) {
                Log::error("Error inesperado en intento #{$attempt}: " . $e->getMessage(), [
                    'exception_class' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $attempt++;
            
            // Esperar antes del siguiente intento (backoff exponencial)
            if ($attempt <= $maxAttempts) {
                $waitTime = pow(2, $attempt - 1); // 2, 4, 8 segundos
                Log::info("Esperando {$waitTime} segundos antes del siguiente intento...");
                sleep($waitTime);
            }
        }

        Log::error('Webhook falló después de todos los intentos', [
            'max_attempts' => $maxAttempts,
            'webhook_url' => $webhookUrl
        ]);
    }

    /**
     * Método para probar la conectividad del webhook
     */
    public function testWebhook()
    {
        $testPayload = [
            'test' => true,
            'timestamp' => now()->toISOString(),
            'message' => 'Prueba de conectividad del webhook',
            'source' => 'laravel_test'
        ];

        Log::info('Iniciando prueba de webhook');
        $this->sendWebhook($testPayload);
        
        return response()->json([
            'message' => 'Prueba de webhook enviada. Revisa los logs para ver el resultado.'
        ]);
    }

    public function consulta(Request $request) 
    {
        $cedula = $request->query('cedula');
        $reportes = Reporte::where('cedula', $cedula)->get();
        return view('reportes.consultar', compact('reportes'));
    }
}