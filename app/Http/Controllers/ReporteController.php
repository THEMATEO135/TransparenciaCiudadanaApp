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
                'ciudad_id' => 'nullable|integer|exists:ciudades,id',
                'proveedor_id' => 'nullable|integer|exists:proveedores,id',
                'descripcion' => 'required|string|max:1000',
                'direccion' => 'nullable|string|max:255',
                'localidad' => 'nullable|string|max:100',
                'barrio' => 'nullable|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'imagenes.*' => 'nullable|image|max:5120', // 5MB por imagen
            ]);

            // Upload de imágenes si existen
            $imagenesUrls = [];
            if ($request->hasFile('imagenes')) {
                $imageUploadService = app(\App\Services\ImageUploadService::class);
                $imagenesUrls = $imageUploadService->uploadReporteImages(
                    $request->file('imagenes'),
                    0 // Temporal, se actualizará después
                );
            }

            // Obtener el estado "pendiente" por defecto
            $estadoPendiente = \App\Models\Estado::where('nombre', 'pendiente')->first();
            if (!$estadoPendiente) {
                // Si no existe, buscar el primer estado activo
                $estadoPendiente = \App\Models\Estado::where('activo', true)->orderBy('orden')->first();
            }

            // Crear el reporte
            $reporte = Reporte::create(array_merge($validated, [
                'imagenes' => $imagenesUrls,
                'estado_id' => $estadoPendiente ? $estadoPendiente->id : 1, // 1 como fallback
            ]));
            Log::info('Reporte creado exitosamente', ['id' => $reporte->id]);

            // Calcular prioridad automáticamente
            $reporte->calcularPrioridad(false);

            // Detectar duplicados en segundo plano
            \App\Jobs\DetectDuplicatesJob::dispatch($reporte)->delay(now()->addSeconds(5));

            // Crear update inicial
            $reporte->agregarComentario(
                'Reporte recibido. Estamos evaluando tu caso.',
                true
            );

            // Crear notificación para admins
            $admins = \App\Models\User::where('role', 'admin')->where('is_active', true)->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::createFor(
                    $admin->id,
                    'nuevo_reporte',
                    'Nuevo reporte recibido',
                    "Se ha recibido un reporte de {$reporte->nombres} sobre " . ($reporte->servicio->nombre ?? 'N/A'),
                    route('admin.reportes.edit', $reporte->id)
                );
            }

            // Invalidar cache de estadísticas del dashboard
            \Cache::forget('dashboard_stats');
            \Cache::forget('dashboard_comparativa_mensual');
            \Cache::forget('dashboard_reportes_por_servicio');

            // Preparar payload con datos adicionales
            $payload = [
                'reporte_id' => $reporte->id,
                'nombres' => $reporte->nombres,
                'correo' => $reporte->correo,
                'telefono' => $reporte->telefono,
                'descripcion' => $reporte->descripcion,
                'direccion' => $reporte->direccion,
                'localidad' => $reporte->localidad,
                'barrio' => $reporte->barrio,
                'estado' => $reporte->estado,
                'prioridad' => $reporte->prioridad,
                'latitude' => $reporte->latitude,
                'longitude' => $reporte->longitude,
                'imagenes' => $reporte->imagenes,
                'created_at_formatted' => $reporte->created_at->format('Y-m-d H:i:s'),
                'servicio' => $reporte->servicio ? [
                    'id' => $reporte->servicio->id,
                    'nombre' => $reporte->servicio->nombre,
                    'descripcion' => $reporte->servicio->descripcion
                ] : null,
                'ciudad' => $reporte->ciudad ? [
                    'id' => $reporte->ciudad->id,
                    'nombre' => $reporte->ciudad->nombre
                ] : null,
                'proveedor' => $reporte->proveedor ? [
                    'id' => $reporte->proveedor->id,
                    'nombre' => $reporte->proveedor->nombre
                ] : null,
            ];

            Log::info('Enviando reporte nuevo a n8n via Job', ['reporte_id' => $reporte->id]);

            // Enviar webhook via Job
            \App\Jobs\SendReportToN8n::dispatch($payload, 'reporte_nuevo');

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
    public function create()
{
    return view('reportes.crear');
}
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
     * Método para probar la conectividad del webhook n8n
     */
    public function testWebhook()
    {
        $testPayload = [
            'test' => true,
            'timestamp' => now()->toISOString(),
            'message' => 'Prueba de conectividad del webhook n8n',
            'source' => 'laravel_test',
            'webhook_url' => env('WEBHOOK_URL'),
        ];

        Log::info('Iniciando prueba de webhook n8n');

        // Enviar webhook usando el Job
        \App\Jobs\SendReportToN8n::dispatch($testPayload, 'test');

        return response()->json([
            'success' => true,
            'message' => 'Webhook de prueba enviado a n8n. El Job fue encolado y se procesará en breve.',
            'webhook_url' => env('WEBHOOK_URL'),
            'note' => 'Revisa los logs de Laravel (storage/logs/laravel.log) y n8n para verificar la recepción.'
        ]);
    }

    public function consulta(Request $request)
    {
        $validated = $request->validate([
            'correo' => 'required|email|max:255'
        ]);

        $reportes = Reporte::where('correo', $validated['correo'])
            ->with(['servicio', 'ciudad', 'proveedor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reportes.consultar', compact('reportes'));
    }

    /**
     * Ver timeline de un reporte específico
     */
    public function timeline($id)
    {
        $reporte = Reporte::with([
            'servicio',
            'ciudad',
            'proveedor',
            'operador',
            'updates' => function($q) {
                $q->visibleCiudadano()->orderBy('created_at', 'desc');
            },
            'duplicados',
            'padre'
        ])->findOrFail($id);

        return view('reportes.timeline', compact('reporte'));
    }

    /**
     * Agregar comentario ciudadano (API)
     */
    public function agregarComentario(Request $request, $id)
    {
        $validated = $request->validate([
            'contenido' => 'required|string|max:1000',
            'correo' => 'required|email', // Verificar que sea el dueño
        ]);

        $reporte = Reporte::findOrFail($id);

        // Verificar que el correo coincida
        if ($reporte->correo !== $validated['correo']) {
            return response()->json([
                'ok' => false,
                'error' => 'No autorizado'
            ], 403);
        }

        $update = \App\Models\ReporteUpdate::create([
            'reporte_id' => $reporte->id,
            'user_id' => null, // Comentario del ciudadano
            'tipo' => 'comentario',
            'contenido' => $validated['contenido'],
            'visible_ciudadano' => true,
        ]);

        // Notificar a admins
        $admins = \App\Models\User::where('role', 'admin')->where('is_active', true)->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::createFor(
                $admin->id,
                'nuevo_comentario',
                'Nuevo comentario en reporte #' . $reporte->id,
                $validated['contenido'],
                route('admin.reportes.edit', $reporte->id)
            );
        }

        // Enviar webhook a n8n
        $payload = [
            'reporte_id' => $reporte->id,
            'comentario_id' => $update->id,
            'contenido' => $validated['contenido'],
            'autor_tipo' => 'ciudadano',
            'correo' => $validated['correo'],
            'created_at_formatted' => $update->created_at->format('Y-m-d H:i:s'),
            'reporte' => [
                'id' => $reporte->id,
                'nombres' => $reporte->nombres,
                'descripcion' => $reporte->descripcion,
                'estado' => $reporte->estado,
                'prioridad' => $reporte->prioridad,
            ]
        ];
        \App\Jobs\SendReportToN8n::dispatch($payload, 'comentario');

        return response()->json([
            'ok' => true,
            'update' => $update
        ]);
    }

    /**
     * Buscar duplicados de un reporte (API)
     */
    public function buscarDuplicados(Request $request)
    {
        $validated = $request->validate([
            'servicio_id' => 'required|integer',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'barrio' => 'nullable|string',
        ]);

        $duplicateService = app(\App\Services\DuplicateDetectionService::class);

        // Crear reporte temporal para búsqueda
        $reporteTemp = new Reporte($validated);
        $sugerencia = $duplicateService->getSugerenciaDuplicado($reporteTemp);

        return response()->json([
            'ok' => true,
            'tiene_duplicado' => !is_null($sugerencia),
            'sugerencia' => $sugerencia
        ]);
    }

    /**
     * Unirse a un reporte existente como duplicado
     */
    public function unirDuplicado(Request $request)
    {
        $validated = $request->validate([
            'reporte_nuevo_id' => 'required|integer|exists:reportes,id',
            'reporte_padre_id' => 'required|integer|exists:reportes,id',
            'correo' => 'required|email',
        ]);

        $reporteNuevo = Reporte::findOrFail($validated['reporte_nuevo_id']);

        // Verificar que el correo coincida
        if ($reporteNuevo->correo !== $validated['correo']) {
            return response()->json([
                'ok' => false,
                'error' => 'No autorizado'
            ], 403);
        }

        $duplicateService = app(\App\Services\DuplicateDetectionService::class);
        $result = $duplicateService->unirDuplicado($reporteNuevo, $validated['reporte_padre_id']);

        return response()->json([
            'ok' => $result,
            'message' => $result ? 'Reporte unido exitosamente' : 'Error al unir reporte'
        ]);
    }
}
