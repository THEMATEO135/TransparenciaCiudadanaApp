<!-- resources/views/powerbi.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Financiero - Power BI</title>
    <!-- Tailwind CSS (si no lo tienes cargado globalmente) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Estilo personalizado para compatibilidad con versiones antiguas de Tailwind -->
    <style>
        .aspect-video {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 */
        }
        .aspect-video iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body class="bg-gray-50">

<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Informe Financiero</h2>
    
    <!-- Contenedor responsivo para el iframe de Power BI -->
    <div class="relative w-full aspect-video max-w-5xl mx-auto shadow-lg rounded-lg overflow-hidden">
        <iframe 
            title="Financial Report"
            src="https://app.powerbi.com/view?r=eyJrIjoiZGU4NGI0MzQtNDAwZi00NzQ4LWJiOWYtZDE5OTA4NzdjMzkyIiwidCI6IjRiZjM4ZWEyLTgzMmQtNDU1Mi1iNTA4LTQyMTU3MGRhNDNmZiIsImMiOjR9"
            frameborder="0"
            allowFullScreen="true">
        </iframe>
    </div>

    <!-- Mensaje informativo para móviles (opcional) -->
    <p class="mt-6 text-center text-sm text-gray-600">
        Este informe se visualiza mejor en pantalla completa. Si ves un error, asegúrate de estar autenticado en Power BI.
    </p>
</div>

</body>
</html>