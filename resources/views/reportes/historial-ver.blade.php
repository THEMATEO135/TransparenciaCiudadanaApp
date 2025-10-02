<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reportes - Transparencia Ciudadana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #ff8c42 0%, #ff6600 50%, #e55a00 100%); min-height: 100vh; padding: 2rem 0; }
        .container-public { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
        .header-public { background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header-public h1 { color: #2d3748; margin: 0; font-size: 1.8rem; font-weight: 700; }
        .header-public .email-info { color: #718096; margin-top: 0.5rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .stat-icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .stat-label { color: #718096; font-size: 0.875rem; margin-bottom: 0.25rem; }
        .stat-value { color: #2d3748; font-size: 2rem; font-weight: 700; }
        .table-container { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .empty-state { text-align: center; padding: 3rem; }
        .empty-state i { font-size: 4rem; color: #cbd5e0; margin-bottom: 1rem; }
        .badge { padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; }
        .btn-back { background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border: none; padding: 0.5rem 1.5rem; border-radius: 8px; text-decoration: none; display: inline-block; transition: all 0.3s; font-weight: 600; }
        .btn-back:hover { background: linear-gradient(135deg, #e55a00, #cc4e00); color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255, 102, 0, 0.3); }
    </style>
</head>
<body>
    <div class="container-public">
        <div class="header-public">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1><i class="fas fa-clipboard-list"></i> Mis Reportes</h1>
                    <div class="email-info"><i class="fas fa-envelope"></i> {{ $email }}</div>
                </div>
                <a href="{{ route('home') }}" class="btn-back">
                    <i class="fas fa-home"></i> Ir al Inicio
                </a>
            </div>
        </div>

        @if($reportes->isEmpty())
            <div class="empty-state" style="background: white; border-radius: 12px;">
                <i class="fas fa-inbox"></i>
                <h3 style="color: #2d3748;">No tienes reportes registrados</h3>
                <p style="color: #718096;">Crea tu primer reporte para comenzar</p>
                <a href="{{ route('home') }}" class="btn-back" style="margin-top: 1rem;">Crear Reporte</a>
            </div>
        @else
            <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-icon">📊</span>
            <div class="stat-label">Total Reportes</div>
            <div class="stat-value">{{ $reportes->count() }}</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">⏳</span>
            <div class="stat-label">Pendientes</div>
            <div class="stat-value">{{ $reportes->where('estado', 'pendiente')->count() }}</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">🔄</span>
            <div class="stat-label">En Proceso</div>
            <div class="stat-value">{{ $reportes->where('estado', 'en_proceso')->count() }}</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">✅</span>
            <div class="stat-label">Resueltos</div>
            <div class="stat-value">{{ $reportes->where('estado', 'resuelto')->count() }}</div>
        </div>
    </div>

            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Servicio</th>
                            <th>Descripción</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($reportes as $reporte)
                        <tr>
                            <td><strong>#{{ $reporte->id }}</strong></td>
                            <td>{{ $reporte->servicio->nombre ?? 'N/A' }}</td>
                            <td>{{ Str::limit($reporte->descripcion, 60) }}</td>
                            <td>{{ $reporte->direccion ?? 'No especificada' }}</td>
                            <td>
                                @if($reporte->estado == 'pendiente')
                                    <span class="badge" style="background: #f39c12; color: white;">⏳ Pendiente</span>
                                @elseif($reporte->estado == 'en_proceso')
                                    <span class="badge" style="background: #3498db; color: white;">🔄 En Proceso</span>
                                @else
                                    <span class="badge" style="background: #27ae60; color: white;">✅ Resuelto</span>
                                @endif
                            </td>
                            <td>{{ $reporte->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>
