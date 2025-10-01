@extends('admin.layouts.admin')

@section('head')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 400px; border-radius: 8px; }
    </style>
@endsection

@section('content')
    <h2 class="text-center my-4">📊 Dashboard - Transparencia Ciudadana</h2>

    <!-- Tarjetas resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Reportes</h5>
                    <p class="card-text fs-4">{{ $totalReportes }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Servicios</h5>
                    <p class="card-text fs-4">{{ $totalServicios }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text fs-4">{{ $totalUsuarios }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <canvas id="chartServicios" class="shadow-sm rounded"></canvas>
        </div>
        <div class="col-md-6 mb-4">
            <canvas id="chartMeses" class="shadow-sm rounded"></canvas>
        </div>
    </div>

    <!-- Mapa -->
    <div class="row">
        <div class="col-12">
            <h4 class="my-3">🗺️ Reportes en el mapa</h4>
            <div id="map"></div>
        </div>
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
