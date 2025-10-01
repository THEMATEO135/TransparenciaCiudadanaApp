<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reporte;
use App\Models\Servicio;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Totales
        $totalReportes = Reporte::count();
        $totalServicios = Servicio::count();
        $totalUsuarios = Reporte::distinct('nombres')->count('nombres');

        // Estadísticas por estado
        $pendientes = Reporte::where('estado', 'pendiente')->count();
        $enProceso = Reporte::where('estado', 'en_proceso')->count();
        $resueltos = Reporte::where('estado', 'resuelto')->count();

        // Comparativa mensual (últimos 6 meses)
        $mesesComparativa = [];
        $valoresComparativa = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $mesesComparativa[] = $fecha->format('M Y');
            $valoresComparativa[] = Reporte::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->count();
        }

        // Comparativa anual (últimos 3 años)
        $añosComparativa = [];
        $valoresAnuales = [];
        for ($i = 2; $i >= 0; $i--) {
            $año = now()->subYears($i)->year;
            $añosComparativa[] = $año;
            $valoresAnuales[] = Reporte::whereYear('created_at', $año)->count();
        }

        // Reportes por servicio
        $labelsServicios = Servicio::pluck('nombre')->toArray();
        $valoresServicios = collect($labelsServicios)->map(function ($servicio) {
            return Reporte::whereHas('servicio', fn($q) => $q->where('nombre', $servicio))->count();
        })->toArray();

        // Reportes por mes (para compatibilidad con la vista antigua)
        $labelsMeses = collect($mesesComparativa);
        $valoresMeses = collect($valoresComparativa);

        // Reportes recientes (últimos 10)
        $reportesRecientes = Reporte::with('servicio')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get();

        // Coordenadas de los reportes (para el mapa de calor del dashboard)
        $coordenadas = Reporte::select('lat', 'lng')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get();

        // Actividad reciente
        $actividadReciente = \App\Models\ActivityLog::with('user')
            ->orderBy('created_at', 'DESC')
            ->limit(15)
            ->get();

        return view('admin.dashboard', compact(
            'totalReportes',
            'totalServicios',
            'totalUsuarios',
            'pendientes',
            'enProceso',
            'resueltos',
            'labelsServicios',
            'valoresServicios',
            'labelsMeses',
            'valoresMeses',
            'mesesComparativa',
            'valoresComparativa',
            'añosComparativa',
            'valoresAnuales',
            'reportesRecientes',
            'coordenadas',
            'actividadReciente'
        ));
    }

    // 👇 Nuevo método para la vista "mapa"
    public function mapa()
    {
        // 🔹 Aquí deberían venir tus coordenadas desde la BD
        $coordenadas = Reporte::select('lat', 'lng', 'servicio_id')
            ->with('servicio:id,nombre')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get()
            ->map(function ($r) {
                return [
                    "lat" => $r->lat,
                    "lng" => $r->lng,
                    "servicio" => $r->servicio->nombre ?? "Desconocido"
                ];
            });

        return view('admin.mapa', compact('coordenadas'));
    }
}
