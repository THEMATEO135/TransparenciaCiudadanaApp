<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reporte - Transparencia Ciudadana</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; }
        .btn { padding: 10px 15px; background: #28a745; color: white; border: none; cursor: pointer; }
        .btn-back { background: #6c757d; }
        .btn-save { background: #007bff; }
    </style>
</head>
<body>
    <h2>✏️ Editar Reporte #{{ $reporte->id }}</h2>

    <form action="{{ route('admin.reportes.update', $reporte) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Estado:</label>
            <select name="estado" required>
                <option value="pendiente" {{ $reporte->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="en_proceso" {{ $reporte->estado == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                <option value="resuelto" {{ $reporte->estado == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
            </select>
        </div>

        <div class="form-group">
            <label>Servicio:</label>
            <select name="servicio_id" required>
                @foreach($servicios as $servicio)
                    <option value="{{ $servicio->id }}" {{ $reporte->servicio_id == $servicio->id ? 'selected' : '' }}>
                        {{ $servicio->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Descripción:</label>
            <textarea name="descripcion" rows="5" required>{{ $reporte->descripcion }}</textarea>
        </div>

        <button type="submit" class="btn btn-save">💾 Guardar Cambios</button>
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-back">⬅️ Volver al Listado</a>
    </form>
</body>
</html>