<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;
use App\Models\ReporteFeedback;

class OperadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.auth');
    }

    /**
     * Dashboard del operador
     */
    public function dashboard()
    {
        $userId = auth()->id();

        // Reportes asignados al operador actual
        $reportesAsignados = Reporte::asignadoA($userId)
            ->with(['servicio', 'ciudad', 'estado'])
            ->get();

        $stats = [
            'total_asignados' => $reportesAsignados->count(),
            'pendientes' => $reportesAsignados->filter(fn($r) => $r->estado && $r->estado->nombre === 'asignado')->count(),
            'en_proceso' => $reportesAsignados->filter(fn($r) => $r->estado && in_array($r->estado->nombre, ['en_proceso', 'en_revision']))->count(),
            'resueltos_hoy' => $reportesAsignados
                ->filter(fn($r) => $r->estado && $r->estado->nombre === 'resuelto' && $r->updated_at->isToday())
                ->count(),
            'vencidos' => $reportesAsignados->filter(fn($r) => $r->estaVencido())->count(),
        ];

        // Cálculos de desempeño
        $reportesResueltos = $reportesAsignados->filter(fn($r) => $r->estado && $r->estado->nombre === 'resuelto');

        $tiempoPromedio = 0;
        if ($reportesResueltos->count() > 0) {
            $totalHoras = 0;
            foreach ($reportesResueltos as $reporte) {
                $totalHoras += $reporte->created_at->diffInHours($reporte->updated_at);
            }
            $tiempoPromedio = round($totalHoras / $reportesResueltos->count(), 1);
        }

        // Calificación promedio
        $feedbacks = ReporteFeedback::whereHas('reporte', function($q) use ($userId) {
            $q->where('assigned_to', $userId);
        })->respondido()->get();

        $calificacionPromedio = $feedbacks->avg('calificacion') ?? 0;
        $npsPromedio = $feedbacks->avg('nps') ?? 0;

        // Reportes por prioridad
        $porPrioridad = [
            'critica' => $reportesAsignados->where('prioridad', 'critica')->count(),
            'alta' => $reportesAsignados->where('prioridad', 'alta')->count(),
            'media' => $reportesAsignados->where('prioridad', 'media')->count(),
            'baja' => $reportesAsignados->where('prioridad', 'baja')->count(),
        ];

        // Lista de reportes (últimos 50)
        $reportes = $reportesAsignados->sortByDesc('prioridad')->take(50);

        return view('admin.operador.dashboard', compact(
            'stats',
            'tiempoPromedio',
            'calificacionPromedio',
            'npsPromedio',
            'porPrioridad',
            'reportes'
        ));
    }

    /**
     * Mis reportes
     */
    public function misReportes(Request $request)
    {
        $userId = auth()->id();

        $query = Reporte::asignadoA($userId)
            ->with(['servicio', 'ciudad', 'proveedor', 'estado']);

        // Filtros
        if ($request->filled('estado')) {
            $query->porEstado($request->estado);
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        $reportes = $query->orderBy('prioridad', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);

        $estados = \App\Models\Estado::activos()->ordenados()->get();

        return view('admin.operador.mis-reportes', compact('reportes', 'estados'));
    }

    /**
     * Aceptar asignación de reporte
     */
    public function aceptarAsignacion($id)
    {
        $reporte = Reporte::findOrFail($id);

        if ($reporte->assigned_to !== auth()->id()) {
            return response()->json([
                'ok' => false,
                'error' => 'Este reporte no está asignado a ti'
            ], 403);
        }

        $reporte->cambiarEstado('en_proceso', 'Operador aceptó el caso y comenzó a trabajar en él.');

        return response()->json([
            'ok' => true,
            'message' => 'Asignación aceptada'
        ]);
    }

    /**
     * Marcar como requiere información
     */
    public function requiereInformacion(Request $request, $id)
    {
        $validated = $request->validate([
            'comentario' => 'required|string|max:1000'
        ]);

        $reporte = Reporte::findOrFail($id);

        if ($reporte->assigned_to !== auth()->id()) {
            return response()->json([
                'ok' => false,
                'error' => 'No autorizado'
            ], 403);
        }

        $reporte->cambiarEstado('requiere_informacion', $validated['comentario']);

        // Enviar email al ciudadano
        \App\Jobs\SendReporteNotificationJob::dispatch($reporte, 'requiere_informacion');

        return response()->json([
            'ok' => true,
            'message' => 'Estado actualizado y ciudadano notificado'
        ]);
    }
}
