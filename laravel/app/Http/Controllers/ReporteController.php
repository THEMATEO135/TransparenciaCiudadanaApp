<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Reporte;

class ReporteController extends Controller
{
    public function index() {
        return view('reportes.crear');
    }

     public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'correo' => 'required|email',
            'telefono' => 'required|string',
            'servicio_id' => 'required|integer|exists:servicios,id',
            'descripcion' => 'required|string',
        ]);

        // Guardamos el reporte SIEMPRE
        $reporte = Reporte::create($validated);

        // Intentamos enviar el webhook, pero no bloqueamos al usuario
        try {
            Http::timeout(5) // Timeout de 5 segundos
                ->retry(2, 1000) // Reintentar 2 veces, con 1 segundo entre intentos
                ->post('https://n8n-webhook-url.com', $reporte->toArray());
        } catch (\Exception $e) {
            // ¡Registramos el error para debugging, pero NO detenemos la ejecución!
            Log::error('Error enviando webhook a n8n: ' . $e->getMessage());
            // Opcional: podrías guardar en una tabla de "webhooks pendientes" para reintentar luego
        }

        // Redirigimos al usuario SIEMPRE, con mensaje de éxito
        //return redirect('/')->with('success', 'Reporte enviado correctamente');
        return response()->json([
    'ok' => true,
    'message' => 'Reporte enviado correctamente',
    'id' => $reporte->id
]);
    }

    public function consulta(Request $request) {
        $cedula = $request->query('cedula');
        $reportes = Reporte::where('cedula', $cedula)->get();
        return view('reportes.consultar', compact('reportes'));
    }
}
