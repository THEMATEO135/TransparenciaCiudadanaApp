@extends('admin.layouts.admin')

@section('title', 'Notificaciones')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-bell me-2"></i> Centro de Notificaciones
                </h1>
                @if($unreadCount > 0)
                <form action="{{ route('admin.notifications.readAll') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-check-double me-1"></i> Marcar todas como leídas
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary me-2">Total: {{ $total }}</span>
                            <span class="badge bg-warning text-dark">No leídas: {{ $unreadCount }}</span>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-secondary {{ !request('filter') ? 'active' : '' }}">
                                Todas
                            </a>
                            <a href="{{ route('admin.notifications.index') }}?filter=unread" class="btn btn-sm btn-outline-secondary {{ request('filter') == 'unread' ? 'active' : '' }}">
                                No leídas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if($notifications->count() > 0)
            <div class="list-group">
                @foreach($notifications as $notification)
                <div class="list-group-item list-group-item-action {{ !$notification->read ? 'bg-light' : '' }} border-start border-4 border-{{ $notification->type == 'nuevo_reporte' ? 'success' : ($notification->type == 'asignacion' ? 'primary' : ($notification->type == 'cambio_estado' ? 'info' : ($notification->type == 'vencimiento_proximo' ? 'danger' : 'secondary'))) }}">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-{{ $notification->type == 'nuevo_reporte' ? 'success' : ($notification->type == 'asignacion' ? 'primary' : ($notification->type == 'cambio_estado' ? 'info' : ($notification->type == 'vencimiento_proximo' ? 'danger' : 'secondary'))) }} me-2">
                                    <i class="fas fa-{{ $notification->type == 'nuevo_reporte' ? 'plus-circle' : ($notification->type == 'asignacion' ? 'user-check' : ($notification->type == 'cambio_estado' ? 'exchange-alt' : ($notification->type == 'comentario_nuevo' ? 'comment' : ($notification->type == 'vencimiento_proximo' ? 'exclamation-triangle' : 'bell')))) }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                </span>
                                @if(!$notification->read)
                                <span class="badge bg-warning text-dark">Nuevo</span>
                                @endif
                            </div>
                            <h6 class="mb-1 {{ !$notification->read ? 'fw-bold' : '' }}">
                                {{ $notification->title }}
                            </h6>
                            <p class="mb-2 text-muted small">
                                {{ $notification->message }}
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                                @if($notification->read)
                                <span class="ms-2">
                                    <i class="fas fa-check me-1"></i>
                                    Leída {{ $notification->read_at->diffForHumans() }}
                                </span>
                                @endif
                            </small>
                        </div>
                        <div class="ms-3 d-flex flex-column align-items-end">
                            @if($notification->link)
                            <a href="{{ $notification->link }}" class="btn btn-sm btn-outline-primary mb-2">
                                <i class="fas fa-arrow-right me-1"></i> Ver detalles
                            </a>
                            @endif
                            @if(!$notification->read)
                            <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-check me-1"></i> Marcar como leída
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
            @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay notificaciones</h5>
                    <p class="text-muted">
                        @if(request('filter') == 'unread')
                        No tienes notificaciones sin leer en este momento.
                        @else
                        Aún no has recibido ninguna notificación.
                        @endif
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh cada 30 segundos
    setInterval(function() {
        // Actualizar solo si hay nuevas notificaciones
        fetch('{{ route('admin.notifications.unread') }}')
            .then(response => response.json())
            .then(data => {
                if (data.count > {{ $unreadCount }}) {
                    location.reload();
                }
            });
    }, 30000);
});
</script>
@endsection
