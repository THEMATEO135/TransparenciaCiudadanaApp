@extends('admin.layouts.admin')

@section('title', 'Dashboard Operador')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-cog"></i> Mi Dashboard</h2>
        <div>
            <span class="badge bg-info fs-6">{{ auth()->user()->name }}</span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="border-left: 4px solid #ff6600 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Asignados</p>
                            <h3 class="mb-0">{{ $stats['total_asignados'] }}</h3>
                        </div>
                        <div class="text-white rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:#ff6600;">
                            <i class="fas fa-clipboard-list fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Pendientes</p>
                            <h3 class="mb-0">{{ $stats['pendientes'] }}</h3>
                        </div>
                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="border-left: 4px solid #17a2b8 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">En Proceso</p>
                            <h3 class="mb-0">{{ $stats['en_proceso'] }}</h3>
                        </div>
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                            <i class="fas fa-cog fa-spin fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="border-left: 4px solid #28a745 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Resueltos Hoy</p>
                            <h3 class="mb-0">{{ $stats['resueltos_hoy'] }}</h3>
                        </div>
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-3x mb-3" style="color: #ff6600;"></i>
                    <h5>Tiempo Promedio</h5>
                    <h2>{{ $tiempoPromedio }}h</h2>
                    <small class="text-muted">Por reporte resuelto</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-3x text-warning mb-3"></i>
                    <h5>Calificación Promedio</h5>
                    <h2>{{ number_format($calificacionPromedio, 1) }}/5</h2>
                    <div class="mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($calificacionPromedio))
                            <i class="fas fa-star text-warning"></i>
                            @else
                            <i class="far fa-star text-warning"></i>
                            @endif
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-thumbs-up fa-3x text-info mb-3"></i>
                    <h5>NPS Promedio</h5>
                    <h2>{{ number_format($npsPromedio, 1) }}/10</h2>
                    <small class="text-muted">
                        @if($npsPromedio >= 9)
                        Promotor
                        @elseif($npsPromedio >= 7)
                        Pasivo
                        @else
                        Detractor
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if($stats['vencidos'] > 0)
    <div class="alert alert-danger d-flex align-items-center mb-4">
        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
        <div>
            <strong>¡Atención!</strong> Tienes {{ $stats['vencidos'] }} reportes vencidos que requieren atención inmediata.
            <a href="{{ route('admin.operador.misReportes') }}?estado=vencidos" class="alert-link">Ver reportes vencidos</a>
        </div>
    </div>
    @endif

    <!-- Prioridad Distribution -->
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header" style="background: #ff6600; color: white;">
                    <h5 class="mb-0"><i class="fas fa-flag"></i> Distribución por Prioridad</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <i class="fas fa-flag text-danger fa-2x mb-2"></i>
                                <h4 class="text-danger">{{ $porPrioridad['critica'] }}</h4>
                                <small class="text-muted">Crítica</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <i class="fas fa-flag text-warning fa-2x mb-2"></i>
                                <h4 class="text-warning">{{ $porPrioridad['alta'] }}</h4>
                                <small class="text-muted">Alta</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <i class="fas fa-flag text-info fa-2x mb-2"></i>
                                <h4 class="text-info">{{ $porPrioridad['media'] }}</h4>
                                <small class="text-muted">Media</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 border rounded">
                                <i class="fas fa-flag text-secondary fa-2x mb-2"></i>
                                <h4 class="text-secondary">{{ $porPrioridad['baja'] }}</h4>
                                <small class="text-muted">Baja</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mis Reportes -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: #ff6600; color: white;">
            <h5 class="mb-0"><i class="fas fa-list"></i> Mis Reportes Recientes</h5>
            <a href="{{ route('admin.operador.misReportes') }}" class="btn btn-light btn-sm">Ver Todos</a>
        </div>
        <div class="card-body">
            @if($reportes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Servicio</th>
                            <th>Ubicación</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportes as $reporte)
                        <tr>
                            <td><strong>#{{ $reporte->id }}</strong></td>
                            <td>{{ $reporte->servicio->nombre ?? 'N/A' }}</td>
                            <td>{{ $reporte->barrio ?? $reporte->localidad ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $reporte->color_prioridad }}">
                                    {{ ucfirst($reporte->prioridad) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $reporte->color_estado }}">
                                    {{ ucfirst(str_replace('_', ' ', $reporte->estado)) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $reporte->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('admin.reportes.edit', $reporte->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($reporte->estado === 'asignado')
                                <button class="btn btn-sm btn-success" onclick="aceptarReporte({{ $reporte->id }})">
                                    <i class="fas fa-check"></i> Aceptar
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                No tienes reportes asignados en este momento
            </p>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
async function aceptarReporte(id) {
    if (!confirm('¿Deseas aceptar este reporte?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/operador/reportes/${id}/aceptar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.ok) {
            alert('Reporte aceptado exitosamente');
            window.location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}
</script>
@endsection
