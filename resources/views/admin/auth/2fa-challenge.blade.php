<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA - Transparencia Ciudadana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .auth-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .btn-primary {
            background: #ff6600;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #e55a00;
        }
        #code {
            text-align: center;
            font-size: 24px;
            letter-spacing: 8px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-mobile-alt" style="font-size: 48px; color: #ff6600; margin-bottom: 15px;"></i>
            <h1>Verificación de Dos Factores</h1>
            <p class="text-muted">Ingresa el código de tu aplicación de autenticación</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.2fa.verify.challenge') }}">
            @csrf
            <div class="mb-4">
                <label for="code" class="form-label text-center w-100">Código de 6 dígitos</label>
                <input type="text" class="form-control" id="code" name="code"
                       placeholder="000000" maxlength="6" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-check me-2"></i>
                Verificar
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('admin.logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               style="color: #667eea; text-decoration: none;">
                <i class="fas fa-sign-out-alt me-1"></i>
                Cerrar sesión
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
