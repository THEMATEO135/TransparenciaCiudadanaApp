

@extends('layouts.app')

@section('title', 'Panel de Reportes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            📊 Reportes Recibidos
        </h2>
        <a href="{{ route('reportes.create') }}" class="btn btn-success shadow-sm">
            ➕ Nuevo Reporte (ciudadano)
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">📑 Listado de reportes</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-primary">
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
                            <td>{{ $reporte->id }}</td>
                            <td>{{ $reporte->nombres }}</td>
                            <td>{{ $reporte->servicio->nombre ?? 'N/A' }}</td>
                            <td>{{ Str::limit($reporte->descripcion, 50) }}</td>
                            <td>
                                <span class="badge bg-{{ $reporte->estado === 'Resuelto' ? 'success' : 'warning' }}">
                                    {{ $reporte->estado ?? 'Pendiente' }}
                                </span>
                            </td>
                            <td>{{ $reporte->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.reportes.edit', $reporte) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    ✏️ Editar
                                </a>
                                <form action="{{ route('admin.reportes.destroy', $reporte) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Seguro que deseas eliminar este reporte?')">
                                        🗑️ Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                🚫 No hay reportes registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
 <div class="d-flex justify-content-center">
    {{ $reportes->links('vendor.pagination.bootstrap-5') }}
</div>


        </div>
    </div>
@endsection


