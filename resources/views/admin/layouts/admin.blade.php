<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administrador') - Transparencia Ciudadana</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('head')

    <!-- Variables globales para JavaScript -->
    <script>
        window.userId = {{ auth()->id() ?? 'null' }};
        window.userEmail = "{{ auth()->user()->email ?? '' }}";
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg admin-navbar" role="navigation" aria-label="Navegación principal del administrador">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}" aria-label="Ir al inicio - Transparencia Ciudadana">Transparencia Ciudadana</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar menú de navegación">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto" role="menubar">
                    <li class="nav-item" role="none">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                           href="{{ route('admin.dashboard') }}"
                           role="menuitem"
                           aria-label="Ver panel de control"
                           aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : 'false' }}">📊 Dashboard</a>
                    </li>
                    <li class="nav-item" role="none">
                        <a class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}"
                           href="{{ route('admin.reportes.index') }}"
                           role="menuitem"
                           aria-label="Gestionar reportes"
                           aria-current="{{ request()->routeIs('admin.reportes.*') ? 'page' : 'false' }}">📑 Reportes</a>
                    </li>
                    <li class="nav-item" role="none">
                        <a class="nav-link {{ request()->routeIs('admin.mapa') ? 'active' : '' }}"
                           href="{{ route('admin.mapa') }}"
                           role="menuitem"
                           aria-label="Ver mapa de calor de reportes"
                           aria-current="{{ request()->routeIs('admin.mapa') ? 'page' : 'false' }}">🗺️ Mapa</a>
                    </li>
                    <li class="nav-item" role="none" style="display: flex; align-items: center; flex-direction: column; padding: 0.5rem 1rem;">
                        <div style="color: #718096; font-size: 0.875rem; margin-bottom: 0.25rem;" aria-label="Usuario actual: {{ session('admin_email', 'admin@example.com') }}">
                            <i class="fas fa-user-circle" aria-hidden="true"></i> {{ session('admin_email', 'admin@example.com') }}
                        </div>
                        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn logout-btn" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;" aria-label="Cerrar sesión del panel de administrador">
                                🚪 Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="content-wrapper fade-in">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



    @yield('scripts')
</body>
</html>

