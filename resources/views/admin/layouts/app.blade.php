<!DOCTYPE html>
<html lang="es">

<style>
    .pagination .page-link {
        font-size: 0.9rem; /* Tamaño normal */
        padding: 0.5rem 0.75rem;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
    }
    .pagination .page-item.active .page-link {
        background-color: #6f42c1;
        border-color: #6f42c1;
    }
</style>

    <style>
    .pagination .page-link {
        color: #6f42c1; /* Púrpura */
        border-radius: 8px;
        margin: 0 2px;
    }
    .pagination .page-link:hover {
        background-color: #6f42c1;
        color: #fff;
    }
    .pagination .active .page-link {
        background-color: #6f42c1;
        border-color: #6f42c1;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Transparencia Ciudadana')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.reportes.index') }}">Transparencia Ciudadana</a>
        </div>
    </nav>

    <div class="container py-4">
        @yield('content')
    </div>
</body>
</html>

