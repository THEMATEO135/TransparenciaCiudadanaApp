<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reporte;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Cache de estadísticas generales (10 minutos)
        $stats = Cache::remember('dashboard_stats', 600, function () {
            return [
                'totalReportes' => Reporte::count(),
                'totalServicios' => Servicio::count(),
                'totalUsuarios' => Reporte::distinct('nombres')->count('nombres'),
                'pendientes' => Reporte::porEstado('pendiente')->count(),
                'enProceso' => Reporte::porEstado('en_proceso')->count(),
                'resueltos' => Reporte::porEstado('resuelto')->count(),
            ];
        });

        $totalReportes = $stats['totalReportes'];
        $totalServicios = $stats['totalServicios'];
        $totalUsuarios = $stats['totalUsuarios'];
        $pendientes = $stats['pendientes'];
        $enProceso = $stats['enProceso'];
        $resueltos = $stats['resueltos'];

        // Cache de comparativas mensuales (30 minutos)
        $comparativaMensual = Cache::remember('dashboard_comparativa_mensual', 1800, function () {
            $mesesComparativa = [];
            $valoresComparativa = [];
            for ($i = 5; $i >= 0; $i--) {
                $fecha = now()->subMonths($i);
                $mesesComparativa[] = $fecha->format('M Y');
                $valoresComparativa[] = Reporte::whereYear('created_at', $fecha->year)
                    ->whereMonth('created_at', $fecha->month)
                    ->count();
            }
            return compact('mesesComparativa', 'valoresComparativa');
        });

        $mesesComparativa = $comparativaMensual['mesesComparativa'];
        $valoresComparativa = $comparativaMensual['valoresComparativa'];

        // Cache de comparativas anuales (1 hora)
        $comparativaAnual = Cache::remember('dashboard_comparativa_anual', 3600, function () {
            $añosComparativa = [];
            $valoresAnuales = [];
            for ($i = 2; $i >= 0; $i--) {
                $año = now()->subYears($i)->year;
                $añosComparativa[] = $año;
                $valoresAnuales[] = Reporte::whereYear('created_at', $año)->count();
            }
            return compact('añosComparativa', 'valoresAnuales');
        });

        $añosComparativa = $comparativaAnual['añosComparativa'];
        $valoresAnuales = $comparativaAnual['valoresAnuales'];

        // Cache de servicios (1 hora - raramente cambian)
        $servicios = Cache::remember('servicios_all', 3600, function () {
            return Servicio::all();
        });

        $labelsServicios = $servicios->pluck('nombre')->toArray();

        // Cache de reportes por servicio (15 minutos)
        $valoresServicios = Cache::remember('dashboard_reportes_por_servicio', 900, function () use ($labelsServicios) {
            return collect($labelsServicios)->map(function ($servicio) {
                return Reporte::whereHas('servicio', fn($q) => $q->where('nombre', $servicio))->count();
            })->toArray();
        });

        // Reportes por mes (para compatibilidad con la vista antigua)
        $labelsMeses = collect($mesesComparativa);
        $valoresMeses = collect($valoresComparativa);

        // Reportes recientes (últimos 10)
        $reportesRecientes = Reporte::with('servicio')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get();

        // Coordenadas de los reportes (para el mapa de calor del dashboard)
        $coordenadas = Reporte::select('latitude as lat', 'longitude as lng', 'servicio_id')
            ->with('servicio:id,nombre')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function($r) {
                return [
                    'lat' => (float)$r->lat,
                    'lng' => (float)$r->lng,
                    'servicio' => $r->servicio->nombre ?? 'N/A'
                ];
            });

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
        // Obtener todos los reportes con coordenadas
        $reportes = Reporte::with('servicio:id,nombre')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($r) {
                return [
                    "id" => $r->id,
                    "titulo" => $r->servicio->nombre ?? 'Reporte sin título',
                    "descripcion" => $r->descripcion ?? 'Sin descripción',
                    "ubicacion" => ($r->direccion ?? '') . ' ' . ($r->barrio ?? '') . ' ' . ($r->localidad ?? ''),
                    "ciudadano" => $r->nombres ?? 'Anónimo',
                    "estado" => ucfirst(str_replace('_', ' ', $r->estado ?? 'pendiente')),
                    "servicio" => $r->servicio->nombre ?? "Desconocido",
                    "lat" => (float)$r->latitude,
                    "lng" => (float)$r->longitude
                ];
            });

        return view('admin.mapa', compact('reportes'));
    }
}