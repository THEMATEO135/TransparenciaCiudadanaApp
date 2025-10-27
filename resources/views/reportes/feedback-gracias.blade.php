<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por tu feedback!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .success-icon {
            font-size: 5rem;
            color: #ff6600;
            animation: scaleIn 0.5s ease-in-out;
        }
        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow text-center">
                    <div class="card-body py-5">
                        <i class="fas fa-check-circle success-icon mb-4 d-block"></i>
                        <h2 class="mb-3">¡Gracias por tu feedback!</h2>
                        <p class="text-muted mb-4">
                            Tu opinión nos ayuda a mejorar el servicio para toda la comunidad.
                        </p>

                        @if(session('resuelto') === false)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Hemos reabierto tu reporte</strong>
                            <p class="mb-0 mt-2">Como indicaste que el problema no está resuelto, tu reporte ha sido reabierto y un operador lo revisará nuevamente.</p>
                        </div>
                        @else
                        <div class="alert alert-success">
                            <i class="fas fa-check"></i>
                            <strong>Tu reporte ha sido cerrado exitosamente</strong>
                            <p class="mb-0 mt-2">Nos alegra saber que pudimos resolver tu problema.</p>
                        </div>
                        @endif

                        <hr class="my-4">

                        <div class="d-grid gap-2">
                            <a href="{{ route('home') }}" class="btn btn-lg text-white" style="background: #ff6600;">
                                <i class="fas fa-home"></i> Volver al Inicio
                            </a>
                            <a href="{{ route('reportes.historial') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-list"></i> Ver Mis Reportes
                            </a>
                        </div>

                        <div class="mt-4 text-muted">
                            <small>
                                <i class="fas fa-envelope"></i>
                                Recibirás un correo de confirmación
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
