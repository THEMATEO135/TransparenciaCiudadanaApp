@extends('admin.layouts.app')
@section('no_navbar', true)
@section('title', 'Timeline - Reporte #' . $reporte->id)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <a href="{{ route('reportes.historial') }}" class="btn btn-outline-primary mb-4">
                <i class="fas fa-arrow-left"></i> Volver a Mis Reportes
            </a>

            <!-- Header del Reporte -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="h4 mb-1">Reporte #{{ $reporte->id }}</h2>
                            <p class="text-muted mb-0">{{ $reporte->servicio->nombre ?? 'N/A' }}</p>
                        </div>
                        <span class="badge bg-{{ $reporte->color_estado }} fs-6">
                            {{ ucfirst(str_replace('_', ' ', $reporte->estado)) }}
                        </span>
                    </div>

                    @if($reporte->prioridad)
                    <div class="alert alert-{{ $reporte->color_prioridad }} py-2 mb-3">
                        <i class="fas fa-flag"></i> Prioridad: <strong>{{ ucfirst($reporte->prioridad) }}</strong>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Reportado por</small>
                            <strong>{{ $reporte->nombres }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Fecha</small>
                            <strong>{{ $reporte->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Ubicación</small>
                            <strong>{{ $reporte->barrio ?? $reporte->localidad ?? 'N/A' }}, {{ $reporte->ciudad->nombre ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Proveedor</small>
                            <strong>{{ $reporte->proveedor->nombre ?? 'No especificado' }}</strong>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div>
                        <small class="text-muted d-block mb-1">Descripción</small>
                        <p class="mb-0">{{ $reporte->descripcion }}</p>
                    </div>

                    @if($reporte->imagenes && count($reporte->imagenes) > 0)
                    <div class="mt-3">
                        <small class="text-muted d-block mb-2">Imágenes adjuntas</small>
                        <div class="row g-2">
                            @foreach($reporte->imagenes as $imagen)
                            <div class="col-6 col-md-4">
                                <img src="{{ $imagen }}" class="img-fluid rounded" alt="Evidencia" style="cursor:pointer;" onclick="window.open('{{ $imagen }}', '_blank')">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($reporte->esDuplicado())
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-link"></i> Este reporte está vinculado al
                        <a href="{{ route('reportes.timeline', $reporte->parent_id) }}">Reporte #{{ $reporte->parent_id }}</a>
                    </div>
                    @endif

                    @if($reporte->tieneDuplicados())
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-users"></i> Este problema afecta a <strong>{{ $reporte->duplicados_count + 1 }}</strong> personas en tu zona
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline de Actualizaciones -->
            <div class="card shadow-sm">
                <div class="card-header" style="background:#ff6600; color:white;">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Timeline de Actualizaciones</h5>
                </div>
                <div class="card-body">
                    @if($reporte->updates->count() > 0)
                    <div class="timeline">
                        @foreach($reporte->updates as $update)
                        <div class="timeline-item mb-4">
                            <div class="d-flex">
                                <div class="timeline-marker me-3">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:#ff6600;">
                                        <i class="fas {{ $update->icono }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <strong>{{ $update->nombre_usuario }}</strong>
                                        <small class="text-muted">{{ $update->created_at->diffForHumans() }}</small>
                                    </div>

                                    @if($update->tipo === 'cambio_estado')
                                    <div class="mb-2">
                                        <span class="badge bg-secondary">{{ $update->estado_anterior }}</span>
                                        <i class="fas fa-arrow-right mx-2"></i>
                                        <span class="badge" style="background:#ff6600;">{{ $update->estado_nuevo }}</span>
                                    </div>
                                    @endif

                                    <p class="mb-0">{{ $update->contenido }}</p>

                                    @if($update->archivo_url)
                                    <div class="mt-2">
                                        <img src="{{ $update->archivo_url }}" class="img-fluid rounded" style="max-width:300px;cursor:pointer;" onclick="window.open('{{ $update->archivo_url }}', '_blank')" alt="Adjunto">
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                        Aún no hay actualizaciones para este reporte
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.badge {
    padding: 0.5em 1em;
}
</style>
@endsection
