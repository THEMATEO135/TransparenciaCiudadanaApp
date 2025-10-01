<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Transparencia Ciudadana</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #f8f9fa; }
        .navbar { margin-bottom: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    </style>

    @yield('head')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Transparencia Ciudadana</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">📊 Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.reportes.index') }}">📑 Reportes</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.mapa') }}">🗺️ Mapa</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>

