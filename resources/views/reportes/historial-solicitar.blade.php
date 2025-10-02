<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ver Mis Reportes - Transparencia Ciudadana</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/transparencia.css') }}">

    <style>
        .verification-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
        }
        .verification-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 3rem;
            max-width: 500px;
            width: 100%;
        }
        .verification-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .verification-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ff6600, #e55a00);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .verification-icon i {
            font-size: 2rem;
            color: white;
        }
        .verification-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        .verification-subtitle {
            color: #718096;
            font-size: 1rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .back-link:hover {
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <div class="verification-header">
                <div class="verification-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h1 class="verification-title">Ver Mis Reportes</h1>
                <p class="verification-subtitle">Ingresa tu correo electrónico para recibir un código de verificación</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('reportes.historial.enviarOtp') }}">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope" style="margin-right: 0.5rem; color: #ff6600;"></i>
                        Correo Electrónico
                    </label>
                    <input type="email"
                           class="form-control"
                           id="email"
                           name="email"
                           placeholder="ejemplo@correo.com"
                           required
                           value="{{ old('email') }}">
                    @error('email')
                        <span style="color: #e53e3e; font-size: 0.875rem; margin-top: 0.5rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="submit-btn" style="width: 100%;">
                    <i class="fas fa-paper-plane" style="margin-right: 0.5rem;"></i>
                    Enviar Código de Verificación
                </button>
            </form>

            <div style="text-align: center;">
                <a href="{{ route('home') }}" class="back-link">
                    <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i>
                    Volver al inicio
                </a>
            </div>
        </div>
    </div>
</body>
</html>
