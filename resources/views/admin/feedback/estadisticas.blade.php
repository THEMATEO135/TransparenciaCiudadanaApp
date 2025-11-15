@extends('admin.layouts.admin')

@section('title', 'Estadísticas de Feedback')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-chart-bar me-2"></i>
            Estadísticas de Feedback Ciudadano
        </h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total de Respuestas</h6>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Resueltos</h6>
                    <h3 class="mb-0">{{ $stats['resueltos'] }}</h3>
                    <small>{{ $stats['total'] > 0 ? round(($stats['resueltos']/$stats['total'])*100, 1) : 0 }}%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">No Resueltos</h6>
                    <h3 class="mb-0">{{ $stats['no_resueltos'] }}</h3>
                    <small>{{ $stats['total'] > 0 ? round(($stats['no_resueltos']/$stats['total'])*100, 1) : 0 }}%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Calificación Promedio</h6>
                    <h3 class="mb-0">{{ round($stats['calificacion_promedio'], 1) }}/5</h3>
                    <small>
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($stats['calificacion_promedio']))
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- NPS Score -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Net Promoter Score (NPS)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h6>Promotores (9-10)</h6>
                            <h2 class="text-success">{{ $stats['promotores'] }}</h2>
                        </div>
                        <div class="col-md-3">
                            <h6>Pasivos (7-8)</h6>
                            <h2 class="text-warning">{{ $stats['pasivos'] }}</h2>
                        </div>
                        <div class="col-md-3">
                            <h6>Detractores (0-6)</h6>
                            <h2 class="text-danger">{{ $stats['detractores'] }}</h2>
                        </div>
                        <div class="col-md-3">
                            <h6>NPS Score</h6>
                            <h2 class="{{ $stats['nps_score'] > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $stats['nps_score'] }}
                            </h2>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 30px;">
                        <div class="progress-bar bg-success" style="width: {{ $stats['total'] > 0 ? ($stats['promotores']/$stats['total'])*100 : 0 }}%">
                            Promotores
                        </div>
                        <div class="progress-bar bg-warning" style="width: {{ $stats['total'] > 0 ? ($stats['pasivos']/$stats['total'])*100 : 0 }}%">
                            Pasivos
                        </div>
                        <div class="progress-bar bg-danger" style="width: {{ $stats['total'] > 0 ? ($stats['detractores']/$stats['total'])*100 : 0 }}%">
                            Detractores
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback List -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Comentarios Recientes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Reporte</th>
                                    <th>Resuelto</th>
                                    <th>Calificación</th>
                                    <th>NPS</th>
                                    <th>Comentario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feedbacks->sortByDesc('respondido_at')->take(20) as $feedback)
                                <tr>
                                    <td>{{ $feedback->respondido_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.reportes.edit', $feedback->reporte_id) }}">
                                            #{{ $feedback->reporte_id }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($feedback->resuelto)
                                            <span class="badge bg-success">Sí</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $feedback->calificacion)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($feedback->nps >= 9) bg-success
                                            @elseif($feedback->nps >= 7) bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ $feedback->nps }}/10
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($feedback->comentario, 50) ?? 'Sin comentario' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay feedback aún</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
