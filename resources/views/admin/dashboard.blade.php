@extends('admin.layouts.admin')

@section('content')
    <h2 class="text-center my-4">📊 Dashboard - Transparencia Ciudadana</h2>

    <!-- Tarjetas resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Reportes</h5>
                    <p class="card-text fs-4">{{ $totalReportes }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Servicios</h5>
                    <p class="card-text fs-4">{{ $totalServicios }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
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
            <canvas id="chartServicios"></canvas>
        </div>
        <div class="col-md-6 mb-4">
            <canvas id="chartMeses"></canvas>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfica de servicios
        new Chart(document.getElementById('chartServicios'), {
            type: 'bar',
            data: {
                labels: @json($labelsServicios),
                datasets: [{
                    label: 'Reportes por Servicio',
                    data: @json($valoresServicios),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            }
        });

        // Gráfica de reportes por mes
        new Chart(document.getElementById('chartMeses'), {
            type: 'line',
            data: {
                labels: @json($labelsMeses),
                datasets: [{
                    label: 'Reportes por Mes',
                    data: @json($valoresMeses),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    fill: false,
                    tension: 0.1
                }]
            }
        });
    </script>
@endsection
