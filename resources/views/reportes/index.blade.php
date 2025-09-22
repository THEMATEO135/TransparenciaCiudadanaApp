@extends('admin.layouts.admin')

@section('content')
    <h2 class="mb-4">📑 Reportes Recibidos</h2>
    <a href="{{ route('reportes.create') }}" class="btn btn-success mb-3">➕ Nuevo Reporte (ciudadano)</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Ciudadano</th>
                <th>Servicio</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($reportes as $reporte)
            <tr>
                <td>{{ $reporte->id }}</td>
                <td>{{ $reporte->nombres }}</td>
                <td>{{ $reporte->servicio->nombre ?? 'N/A' }}</td>
                <td>{{ Str::limit($reporte->descripcion, 50) }}</td>
                <td>{{ $reporte->estado }}</td>
                <td>{{ $reporte->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <a href="{{ route('admin.reportes.edit', $reporte) }}" class="btn btn-primary btn-sm">✏️ Editar</a>
                    <form action="{{ route('admin.reportes.destroy', $reporte) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Seguro que deseas eliminar este reporte?')">🗑️ Eliminar</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $reportes->links() }}
@endsection
