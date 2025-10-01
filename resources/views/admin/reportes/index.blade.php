@extends('admin.layouts.admin')

@section('content')
    <h2 class="mb-4">📊 Reportes Recibidos</h2>

    <!-- Botón nuevo reporte -->
    <div class="mb-3">
        <a href="{{ route('reportes.create') }}" class="btn btn-primary">
            ➕ Nuevo Reporte (ciudadano)
        </a>
    </div>

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <!-- Tabla de reportes -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
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
                            <span class="badge 
                                @if($reporte->estado == 'Pendiente') bg-warning 
                                @elseif($reporte->estado == 'Resuelto') bg-success 
                                @else bg-secondary 
                                @endif">
                                {{ $reporte->estado ?? 'Sin estado' }}
                            </span>
                        </td>
                        <td>{{ $reporte->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.reportes.edit', $reporte) }}" class="btn btn-sm btn-warning">
                                ✏️ Editar
                            </a>
                            <form action="{{ route('admin.reportes.destroy', $reporte) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Seguro que deseas eliminar este reporte?')">
                                    🗑️ Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No hay reportes registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        {{ $reportes->links() }}
    </div>
@endsection
