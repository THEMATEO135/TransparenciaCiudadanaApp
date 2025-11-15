<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Transparencia Ciudadana</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ff6600;
        }
        .header h1 {
            color: #ff6600;
            margin: 0;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #ff6600;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
        }
        td {
            padding: 6px 5px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transparencia Ciudadana</h1>
        <p>Reporte de Incidencias</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Ciudadano</th>
                <th>Servicio</th>
                <th>Estado</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportes as $reporte)
            <tr>
                <td>{{ $reporte->id }}</td>
                <td>{{ $reporte->created_at->format('d/m/Y') }}</td>
                <td>{{ $reporte->nombres }}</td>
                <td>{{ $reporte->servicio->nombre ?? 'N/A' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $reporte->estado)) }}</td>
                <td>{{ Str::limit($reporte->descripcion, 80) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No hay reportes para exportar</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total de reportes: {{ count($reportes) }}</p>
        <p>&copy; {{ date('Y') }} Transparencia Ciudadana - Todos los derechos reservados</p>
    </div>
</body>
</html>
