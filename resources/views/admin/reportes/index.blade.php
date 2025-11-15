@extends('admin.layouts.admin')

@section('title', 'Reportes')

@section('content')
    <header class="page-header">
        <h1 class="page-title" data-icon="📑">Reportes Recibidos</h1>
        <div class="page-actions">
            <a href="{{ route('reportes.create') }}" class="btn btn-success" aria-label="Crear un nuevo reporte">
                ➕ Nuevo Reporte
            </a>
            <a href="{{ route('admin.mapa') }}" class="btn btn-info" aria-label="Ver reportes en el mapa de calor">
                🗺️ Ver Mapa
            </a>
        </div>
    </header>

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show slide-in" role="alert">
            <strong>✅ Éxito:</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar alerta de éxito"></button>
        </div>
    @endif

    <!-- Estadísticas rápidas -->
    <section class="stats-grid mb-4" aria-label="Estadísticas rápidas de reportes">
        <div class="stat-card" role="article" aria-labelledby="stat-total-index">
            <span class="stat-icon" aria-hidden="true">📋</span>
            <div class="stat-label" id="stat-total-index">Total Reportes</div>
            <div class="stat-value" aria-label="Total de reportes: {{ $reportes->total() }}">{{ $reportes->total() }}</div>
        </div>
        <div class="stat-card" role="article" aria-labelledby="stat-pendientes-index">
            <span class="stat-icon" aria-hidden="true">⏳</span>
            <div class="stat-label" id="stat-pendientes-index">Pendientes</div>
            <div class="stat-value" aria-label="Reportes pendientes: {{ $totalPendientes }}">{{ $totalPendientes }}</div>
        </div>
        <div class="stat-card" role="article" aria-labelledby="stat-resueltos-index">
            <span class="stat-icon" aria-hidden="true">✅</span>
            <div class="stat-label" id="stat-resueltos-index">Resueltos</div>
            <div class="stat-value" aria-label="Reportes resueltos: {{ $totalResueltos }}">{{ $totalResueltos }}</div>
        </div>
        <div class="stat-card" role="article" aria-labelledby="stat-pagina-index">
            <span class="stat-icon" aria-hidden="true">📄</span>
            <div class="stat-label" id="stat-pagina-index">En esta página</div>
            <div class="stat-value" aria-label="Reportes en esta página: {{ $reportes->count() }}">{{ $reportes->count() }}</div>
        </div>
    </section>

    <!-- Tabla de reportes -->
    <div class="table-container fade-in">
        <table class="table" role="table" aria-label="Lista de reportes recibidos">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Ciudadano</th>
                    <th scope="col">Servicio</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Fecha</th>
                    <th scope="col" class="text-center">Acciones</th>
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
                            @if($reporte->estado)
                                <span class="badge" style="background-color: {{ $reporte->estado->color }}; color: white;">
                                    {{ $reporte->estado->icono }} {{ $reporte->estado->etiqueta }}
                                </span>
                            @else
                                <span class="badge badge-secondary">Sin estado</span>
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
