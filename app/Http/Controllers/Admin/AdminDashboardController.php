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

        // Reportes por servicio
        $labelsServicios = Servicio::pluck('nombre');
        $valoresServicios = $labelsServicios->map(function ($servicio) {
            return Reporte::whereHas('servicio', fn($q) => $q->where('nombre', $servicio))->count();
        });

        // Reportes por mes
        $labelsMeses = Reporte::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes")
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('mes');
        $valoresMeses = $labelsMeses->map(function ($mes) {
            return Reporte::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$mes])->count();
        });

        // Coordenadas de los reportes (para el mapa de calor del dashboard)
        $coordenadas = Reporte::select('lat', 'lng')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get();

        return view('admin.dashboard', compact(
            'totalReportes',
            'totalServicios',
            'totalUsuarios',
            'labelsServicios',
            'valoresServicios',
            'labelsMeses',
            'valoresMeses',
            'coordenadas'
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
