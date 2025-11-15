<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr style="background-color: #ff6600; color: white; font-weight: bold;">
            <th>ID</th>
            <th>Fecha</th>
            <th>Ciudadano</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Servicio</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Descripción</th>
            <th>Dirección</th>
            <th>Ciudad</th>
            <th>Proveedor</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reportes as $reporte)
        <tr>
            <td>{{ $reporte->id }}</td>
            <td>{{ $reporte->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $reporte->nombres }}</td>
            <td>{{ $reporte->correo }}</td>
            <td>{{ $reporte->telefono }}</td>
            <td>{{ $reporte->servicio->nombre ?? 'N/A' }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $reporte->estado)) }}</td>
            <td>{{ ucfirst($reporte->prioridad ?? 'N/A') }}</td>
            <td>{{ $reporte->descripcion }}</td>
            <td>{{ $reporte->direccion ?? 'N/A' }}</td>
            <td>{{ $reporte->ciudad->nombre ?? 'N/A' }}</td>
            <td>{{ $reporte->proveedor->nombre ?? 'N/A' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="12" style="text-align: center;">No hay reportes para exportar</td>
        </tr>
        @endforelse
    </tbody>
</table>
