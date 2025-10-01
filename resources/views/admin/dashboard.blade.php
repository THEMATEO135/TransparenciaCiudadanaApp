@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .chart-container {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 100%;
        }
        .chart-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title" data-icon="📊">Dashboard</h1>
        <div class="page-actions">
            <a href="{{ route('admin.reportes.index') }}" class="btn btn-primary">
                📑 Ver Todos los Reportes
            </a>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="stats-grid">
        <div class="stat-card slide-in">
            <span class="stat-icon">📋</span>
            <div class="stat-label">Total Reportes</div>
            <div class="stat-value">{{ $totalReportes }}</div>
        </div>
        <div class="stat-card slide-in" style="animation-delay: 0.1s;">
            <span class="stat-icon">✅</span>
            <div class="stat-label">Servicios Activos</div>
            <div class="stat-value">{{ $totalServicios }}</div>
        </div>
        <div class="stat-card slide-in" style="animation-delay: 0.2s;">
            <span class="stat-icon">👥</span>
            <div class="stat-label">Usuarios Registrados</div>
            <div class="stat-value">{{ $totalUsuarios }}</div>
        </div>
        <div class="stat-card slide-in" style="animation-delay: 0.3s;">
            <span class="stat-icon">📊</span>
            <div class="stat-label">Promedio Mensual</div>
            <div class="stat-value">{{ $totalReportes > 0 ? round($totalReportes / max(count($labelsMeses), 1)) : 0 }}</div>
        </div>
    </div>

    <!-- Gráficas -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="chart-container fade-in">
                <div class="chart-title">
                    <span>📊</span> Reportes por Servicio
                </div>
                <canvas id="chartServicios"></canvas>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="chart-container fade-in" style="animation-delay: 0.1s;">
                <div class="chart-title">
                    <span>📅</span> Tendencia Mensual
                </div>
                <canvas id="chartMeses"></canvas>
            </div>
        </div>
    </div>

    <!-- Mapa de Reportes -->
    <div class="fade-in" style="animation-delay: 0.2s;">
        <div class="page-header">
            <h2 class="page-title" data-icon="🗺️" style="font-size: 1.5rem;">Mapa de Reportes</h2>
        </div>
        <div id="map"></div>
    </div>
@endsection

@section('scripts')
    <!-- Librerías -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

    <!-- Variables globales desde Laravel -->
    <script>
        window.labelsServicios = @json($labelsServicios);
        window.valoresServicios = @json($valoresServicios);
        window.labelsMeses = @json($labelsMeses);
        window.valoresMeses = @json($valoresMeses);
        window.coordenadas = @json($coordenadas);
    </script>

    <!-- Script principal -->
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
