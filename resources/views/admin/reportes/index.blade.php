<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Transparencia Ciudadana</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions a { margin-right: 10px; text-decoration: none; }
        .btn { padding: 5px 10px; background: #007bff; color: white; border-radius: 3px; }
        .btn-danger { background: #dc3545; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>📊 Reportes Recibidos</h2>
    <a href="{{ route('reportes.create') }}" class="btn">➕ Nuevo Reporte (ciudadano)</a>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <table>
        <tr>
            <th>ID</th>
            <th>Ciudadano</th>
            <th>Servicio</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
        @foreach($reportes as $reporte)
        <tr>
            <td>{{ $reporte->id }}</td>
            <td>{{ $reporte->nombres }}</td>
            <td>{{ $reporte->servicio->nombre ?? 'N/A' }}</td>
            <td>{{ Str::limit($reporte->descripcion, 50) }}</td>
            <td>{{ $reporte->estado }}</td>
            <td>{{ $reporte->created_at->format('Y-m-d H:i') }}</td>
            <td class="actions">
                <a href="{{ route('admin.reportes.edit', $reporte) }}" class="btn">✏️ Editar</a>
                <form action="{{ route('admin.reportes.destroy', $reporte) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este reporte?')">🗑️ Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

    {{ $reportes->links() }}
</body>
</html>