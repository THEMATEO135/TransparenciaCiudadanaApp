<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reporte;
use Illuminate\Http\Request;
use App\Exports\ReportesExport;
use Barryvdh\DomPDF\Facade\Pdf;

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
            $query->where('estado', $request->estado);
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

        return view('admin.reportes.index', compact('reportes', 'servicios'));
    }

    // Mostrar formulario de edición
    public function edit(Reporte $reporte)
    {
        $servicios = \App\Models\Servicio::all(); // Para el select
        return view('admin.reportes.edit', compact('reporte', 'servicios'));
    }

    // Actualizar reporte
    public function update(Request $request, Reporte $reporte)
    {
        $validated = $request->validate([
            'estado' => 'required|string|in:pendiente,en_proceso,resuelto',
            'servicio_id' => 'required|integer|exists:servicios,id',
            'descripcion' => 'required|string',
        ]);

        $changes = [];
        foreach ($validated as $key => $value) {
            if ($reporte->$key != $value) {
                $changes[$key] = ['old' => $reporte->$key, 'new' => $value];
            }
        }

        $reporte->update($validated);

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