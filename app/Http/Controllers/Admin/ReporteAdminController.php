<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reporte;
use App\Models\Servicio;
use Illuminate\Http\Request;
use App\Exports\ReportesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ReporteAdminController extends Controller
{
    // Mostrar listado de reportes con filtros avanzados
    public function index(Request $request)
    {
        $query = Reporte::with('servicio');

        // Filtro por fecha
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // Filtro por servicio
        if ($request->filled('servicio_id')) {
            $query->where('servicio_id', $request->servicio_id);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->porEstado($request->estado);
        }

        // Filtro por ubicación (búsqueda en dirección)
        if ($request->filled('ubicacion')) {
            $query->where('direccion', 'like', '%' . $request->ubicacion . '%');
        }

        // Filtro por búsqueda general
        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $reportes = $query->orderBy('created_at', 'DESC')->paginate(15);
        $servicios = \App\Models\Servicio::all();
        $estados = \App\Models\Estado::activos()->ordenados()->get();

        // Obtener contadores totales (no solo de la página actual)
        $totalPendientes = \App\Models\Reporte::porEstado('pendiente')->count();
        $totalResueltos = \App\Models\Reporte::porEstado('resuelto')->count();
        $totalEnProceso = \App\Models\Reporte::porEstado('en_proceso')->count();

        return view('admin.reportes.index', compact('reportes', 'servicios', 'estados', 'totalPendientes', 'totalResueltos', 'totalEnProceso'));
    }

    // Mostrar formulario de edición
    public function edit(Reporte $reporte)
    {
        $servicios = \App\Models\Servicio::all();
        $estados = \App\Models\Estado::activos()->ordenados()->get();
        return view('admin.reportes.edit', compact('reporte', 'servicios', 'estados'));
    }

    // Actualizar reporte
    public function update(Request $request, Reporte $reporte)
    {
        $validated = $request->validate([
            'estado_id' => 'required|integer|exists:estados,id',
            'servicio_id' => 'required|integer|exists:servicios,id',
            'descripcion' => 'required|string',
            'notas_admin' => 'nullable|string',
        ]);

        $estadoNuevoId = $validated['estado_id'];
        $estadoAnteriorId = $reporte->estado_id;
        $changes = [];
        $updatesPendientes = [];

        $camposActualizables = [
            'servicio_id' => $validated['servicio_id'],
            'descripcion' => $validated['descripcion'],
        ];

        if (array_key_exists('notas_admin', $validated)) {
            $camposActualizables['notas_admin'] = $validated['notas_admin'];
        }

        foreach ($camposActualizables as $campo => $valor) {
            if ($reporte->$campo != $valor) {
                $changes[$campo] = [
                    'old' => $reporte->$campo,
                    'new' => $valor,
                ];
                $reporte->$campo = $valor;
            }
        }

        if (!empty($changes)) {
            $reporte->save();

            if (isset($changes['servicio_id'])) {
                $servicioAnterior = !empty($changes['servicio_id']['old'])
                    ? Servicio::find($changes['servicio_id']['old'])
                    : null;
                $servicioNuevo = Servicio::find($changes['servicio_id']['new']);

                $updatesPendientes[] = [
                    'mensaje' => sprintf(
                        "Servicio actualizado de '%s' a '%s'.",
                        $servicioAnterior->nombre ?? 'No especificado',
                        $servicioNuevo->nombre ?? 'No especificado'
                    ),
                    'tipo' => 'actualizacion',
                ];
            }

            if (isset($changes['descripcion'])) {
                $updatesPendientes[] = [
                    'mensaje' => "La descripcion del reporte fue actualizada: \"" . Str::limit($changes['descripcion']['new'], 180) . "\"",
                    'tipo' => 'actualizacion',
                ];
            }

            if (isset($changes['notas_admin'])) {
                $updatesPendientes[] = [
                    'mensaje' => 'Notas internas actualizadas por el equipo.',
                    'tipo' => 'sistema',
                    'visible' => false,
                ];
            }
        }

        if ($estadoNuevoId !== $estadoAnteriorId) {
            $changes['estado_id'] = [
                'old' => $estadoAnteriorId,
                'new' => $estadoNuevoId,
            ];
            $reporte->cambiarEstado($estadoNuevoId);
        }

        foreach ($updatesPendientes as $update) {
            $reporte->registrarUpdate(
                $update['mensaje'],
                $update['tipo'] ?? 'actualizacion',
                $update['visible'] ?? true,
                $update['extra'] ?? []
            );
        }

        // Invalidar cache de estadísticas del dashboard
        \Cache::forget('dashboard_stats');
        \Cache::forget('dashboard_comparativa_mensual');
        \Cache::forget('dashboard_reportes_por_servicio');

        // Registrar actividad
        \App\Models\ActivityLog::log(
            'update',
            "Reporte #{$reporte->id} actualizado",
            'Reporte',
            $reporte->id,
            $changes
        );

        return redirect()->route('admin.reportes.index')
            ->with('success', 'Reporte actualizado correctamente');
    }

    // Eliminar reporte
    public function destroy(Reporte $reporte)
    {
        $reporteId = $reporte->id;
        $reporte->delete();

        // Registrar actividad
        \App\Models\ActivityLog::log(
            'delete',
            "Reporte #{$reporteId} eliminado",
            'Reporte',
            $reporteId
        );

        return redirect()->route('admin.reportes.index')
            ->with('success', 'Reporte eliminado correctamente');
    }

    // Exportar a Excel
    public function exportExcel(Request $request)
    {
        $query = Reporte::with('servicio');

        // Aplicar los mismos filtros que en index
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        if ($request->filled('servicio_id')) {
            $query->where('servicio_id', $request->servicio_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $reportes = $query->orderBy('created_at', 'DESC')->get();
        $export = new ReportesExport($reportes);

        // Crear archivo Excel manualmente
        $filename = 'reportes_' . date('Y-m-d_His') . '.xls';

        return response()->streamDownload(function() use ($export) {
            echo view('admin.exports.reportes-excel', [
                'reportes' => $export->reportes
            ])->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    // Exportar a PDF
    public function exportPdf(Request $request)
    {
        $query = Reporte::with('servicio');

        // Aplicar los mismos filtros que en index
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        if ($request->filled('servicio_id')) {
            $query->where('servicio_id', $request->servicio_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $reportes = $query->orderBy('created_at', 'DESC')->get();

        $pdf = Pdf::loadView('admin.exports.reportes-pdf', compact('reportes'));

        return $pdf->download('reportes_' . date('Y-m-d_His') . '.pdf');
    }
}
