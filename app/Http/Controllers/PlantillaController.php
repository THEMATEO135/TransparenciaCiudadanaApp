<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantillaRespuesta;

class PlantillaController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.auth');
    }

    /**
     * Listar plantillas
     */
    public function index()
    {
        $plantillas = PlantillaRespuesta::orderBy('tipo')
                                       ->orderBy('nombre')
                                       ->get();

        return view('admin.plantillas.index', compact('plantillas'));
    }

    /**
     * Crear plantilla
     */
    public function create()
    {
        $variables = PlantillaRespuesta::getVariablesDisponibles();
        return view('admin.plantillas.create', compact('variables'));
    }

    /**
     * Guardar plantilla
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:plantillas_respuesta',
            'asunto' => 'required|string|max:255',
            'contenido' => 'required|string',
            'tipo' => 'required|in:resolucion,informacion,mantenimiento,escalado,otro',
            'activa' => 'boolean',
        ]);

        PlantillaRespuesta::create($validated);

        return redirect()->route('admin.plantillas.index')
                        ->with('success', 'Plantilla creada exitosamente');
    }

    /**
     * Editar plantilla
     */
    public function edit($id)
    {
        $plantilla = PlantillaRespuesta::findOrFail($id);
        $variables = PlantillaRespuesta::getVariablesDisponibles();

        return view('admin.plantillas.edit', compact('plantilla', 'variables'));
    }

    /**
     * Actualizar plantilla
     */
    public function update(Request $request, $id)
    {
        $plantilla = PlantillaRespuesta::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:plantillas_respuesta,nombre,' . $id,
            'asunto' => 'required|string|max:255',
            'contenido' => 'required|string',
            'tipo' => 'required|in:resolucion,informacion,mantenimiento,escalado,otro',
            'activa' => 'boolean',
        ]);

        $plantilla->update($validated);

        return redirect()->route('admin.plantillas.index')
                        ->with('success', 'Plantilla actualizada exitosamente');
    }

    /**
     * Eliminar plantilla
     */
    public function destroy($id)
    {
        $plantilla = PlantillaRespuesta::findOrFail($id);
        $plantilla->delete();

        return redirect()->route('admin.plantillas.index')
                        ->with('success', 'Plantilla eliminada');
    }

    /**
     * Vista previa de plantilla
     */
    public function preview(Request $request, $id)
    {
        $plantilla = PlantillaRespuesta::findOrFail($id);

        // Obtener un reporte de ejemplo para la vista previa
        $reporte = \App\Models\Reporte::with(['servicio', 'ciudad', 'proveedor'])->first();

        if (!$reporte) {
            return response()->json([
                'ok' => false,
                'error' => 'No hay reportes en el sistema para generar vista previa'
            ]);
        }

        $procesado = $plantilla->procesar($reporte);

        return response()->json([
            'ok' => true,
            'asunto' => $procesado['asunto'],
            'contenido' => $procesado['contenido']
        ]);
    }

    /**
     * Enviar email usando plantilla
     */
    public function enviarEmail(Request $request)
    {
        $validated = $request->validate([
            'plantilla_id' => 'required|exists:plantillas_respuesta,id',
            'reporte_id' => 'required|exists:reportes,id',
        ]);

        $plantilla = PlantillaRespuesta::findOrFail($validated['plantilla_id']);
        $reporte = \App\Models\Reporte::with(['servicio', 'ciudad', 'proveedor'])
                                      ->findOrFail($validated['reporte_id']);

        $procesado = $plantilla->procesar($reporte);

        // Enviar email
        \Mail::send([], [], function ($message) use ($reporte, $procesado) {
            $message->to($reporte->correo)
                    ->subject($procesado['asunto'])
                    ->setBody($procesado['contenido'], 'text/html');
        });

        // Incrementar contador de uso
        $plantilla->incrementarUso();

        // Registrar en updates
        $reporte->agregarComentario(
            'Email enviado: ' . $procesado['asunto'],
            true
        );

        return response()->json([
            'ok' => true,
            'message' => 'Email enviado exitosamente'
        ]);
    }
}
