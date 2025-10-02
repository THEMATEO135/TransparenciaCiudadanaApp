<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código - Transparencia Ciudadana</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/transparencia.css') }}">
    <style>
        .verification-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; }
        .verification-card { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); padding: 3rem; max-width: 500px; width: 100%; }
        .verification-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #ff6600, #e55a00); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
        .verification-icon i { font-size: 2rem; color: white; }
        .verification-title { font-size: 1.8rem; font-weight: 700; color: #2d3748; margin-bottom: 0.5rem; text-align: center; }
        .verification-subtitle { color: #718096; font-size: 1rem; text-align: center; margin-bottom: 2rem; }
        .code-input { font-size: 2rem; text-align: center; letter-spacing: 1rem; font-weight: 700; }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <div class="verification-icon"><i class="fas fa-shield-alt"></i></div>
            <h1 class="verification-title">Verificar Código</h1>
            <p class="verification-subtitle">Ingresa el código de 6 dígitos enviado a<br><strong>{{ $email }}</strong></p>

            @if(session('error'))
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('reportes.historial.verificarOtp') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <div class="form-group">
                    <input type="text" class="form-control code-input" name="code" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autofocus>
                </div>
                <button type="submit" class="submit-btn" style="width: 100%;"><i class="fas fa-check" style="margin-right: 0.5rem;"></i>Verificar Código</button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="{{ route('reportes.historial') }}" style="color: #667eea; text-decoration: none;"><i class="fas fa-arrow-left"></i> Solicitar nuevo código</a>
            </div>
        </div>
    </div>
</body>
</html>
