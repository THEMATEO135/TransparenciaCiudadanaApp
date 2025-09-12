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

            // Intentar enviar webhook (no bloquear si falla)
            try {
                if (env('WEBHOOK_URL')) {
                    Http::timeout(5)
                        ->retry(2, 1000)
                        ->post(env('WEBHOOK_URL'), $reporte->toArray());
                }
            } catch (\Exception $e) {
                Log::warning('Error enviando webhook: ' . $e->getMessage());
            }

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

    public function consulta(Request $request) 
    {
        $cedula = $request->query('cedula');
        $reportes = Reporte::where('cedula', $cedula)->get();
        return view('reportes.consultar', compact('reportes'));
    }
}