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
        $totalUsuarios = User::count();

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

        return view('admin.dashboard', compact(
            'totalReportes',
            'totalServicios',
            'totalUsuarios',
            'labelsServicios',
            'valoresServicios',
            'labelsMeses',
            'valoresMeses'
        ));
    }
}
