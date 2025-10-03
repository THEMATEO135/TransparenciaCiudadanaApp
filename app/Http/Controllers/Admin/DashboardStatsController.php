<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reporte;
use Illuminate\Support\Facades\Cache;

class DashboardStatsController extends Controller
{
    /**
     * Retornar estadísticas actualizadas en JSON para actualizaciones en tiempo real
     */
    public function stats()
    {
        $stats = Cache::remember('dashboard_stats', 60, function () {
            return [
                'totalReportes' => Reporte::count(),
                'pendientes' => Reporte::where('estado', 'pendiente')->count(),
                'enProceso' => Reporte::where('estado', 'en_proceso')->count(),
                'resueltos' => Reporte::where('estado', 'resuelto')->count(),
            ];
        });

        return response()->json($stats);
    }
}
