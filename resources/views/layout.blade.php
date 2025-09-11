
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transparencia Ciudadana</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/script.js') }}" defer></script>
</head>
<body>
    <header>
        <h1>Transparencia Ciudadana - Centro de Reportes</h1>
        <p>Reporta fallas de manera rápida y sencilla en nuestros servicios principales</p>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>
