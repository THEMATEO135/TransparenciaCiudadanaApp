@extends('admin.layouts.admin')

@section('title', 'Reportes')

@section('content')
    <div class="page-header">
        <h1 class="page-title" data-icon="📑">Reportes Recibidos</h1>
        <div class="page-actions">
            <a href="{{ route('reportes.create') }}" class="btn btn-success">
                ➕ Nuevo Reporte
            </a>
            <a href="{{ route('admin.mapa') }}" class="btn btn-info">
                🗺️ Ver Mapa
            </a>
        </div>
    </div>

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show slide-in" role="alert">
            <strong>✅ Éxito:</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <!-- Estadísticas rápidas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <span class="stat-icon">📋</span>
            <div class="stat-label">Total Reportes</div>
            <div class="stat-value">{{ $reportes->total() }}</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">⏳</span>
            <div class="stat-label">Pendientes</div>
            <div class="stat-value">{{ $reportes->where('estado', 'Pendiente')->count() }}</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">✅</span>
            <div class="stat-label">Resueltos</div>
            <div class="stat-value">{{ $reportes->where('estado', 'Resuelto')->count() }}</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">📄</span>
            <div class="stat-label">En esta página</div>
            <div class="stat-value">{{ $reportes->count() }}</div>
        </div>
    </div>

    <!-- Tabla de reportes -->
    <div class="table-container fade-in">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ciudadano</th>
                    <th>Servicio</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportes as $reporte)
                    <tr>
                        <td><strong>#{{ $reporte->id }}</strong></td>
                        <td>
                            <div style="font-weight: 600;">{{ $reporte->nombres }}</div>
                            <small style="color: var(--dark-gray);">{{ $reporte->email }}</small>
                        </td>
                        <td>
                            <span style="color: var(--primary-color); font-weight: 500;">
                                {{ $reporte->servicio->nombre ?? 'N/A' }}
                            </span>
                        </td>
                        <td>{{ Str::limit($reporte->descripcion, 60) }}</td>
                        <td>
                            @if($reporte->estado == 'Pendiente')
                                <span class="badge badge-warning">⏳ Pendiente</span>
                            @elseif($reporte->estado == 'Resuelto')
                                <span class="badge badge-success">✅ Resuelto</span>
                            @elseif($reporte->estado == 'En Proceso')
                                <span class="badge badge-info">🔄 En Proceso</span>
                            @else
                                <span class="badge badge-secondary">{{ $reporte->estado ?? 'Sin estado' }}</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $reporte->created_at->format('d/m/Y') }}</div>
                            <small style="color: var(--dark-gray);">{{ $reporte->created_at->format('H:i') }}</small>
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                <a href="{{ route('admin.reportes.edit', $reporte) }}"
                                   class="btn btn-sm btn-warning"
                                   title="Editar">
                                    ✏️
                                </a>
                                <form action="{{ route('admin.reportes.destroy', $reporte) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar este reporte?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Eliminar">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 3rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
                            <div style="color: var(--dark-gray); font-size: 1.1rem;">
                                No hay reportes registrados
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($reportes->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $reportes->links() }}
        </div>
    @endif
@endsection
