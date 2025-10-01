<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador - Transparencia Ciudadana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-container {
            max-width: 480px;
            width: 100%;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }
        .login-header {
            background: linear-gradient(135deg, #ff6600, #e55a00);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .login-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            display: block;
            animation: bounce 1s ease-in-out;
        }
        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .login-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }
        .login-body {
            padding: 2.5rem 2rem;
        }
        .form-floating label {
            color: #6c757d;
        }
        .form-floating .form-control:focus ~ label {
            color: #ff6600;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #ff6600;
            box-shadow: 0 0 0 4px rgba(255,102,0,0.1);
        }
        .btn-login {
            background: linear-gradient(135deg, #ff6600, #e55a00);
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            color: white;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(255,102,0,0.3);
            margin-top: 1rem;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #e55a00, #cc4600);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255,102,0,0.4);
            color: white;
        }
        .form-check-input:checked {
            background-color: #ff6600;
            border-color: #ff6600;
        }
        .form-check-input:focus {
            border-color: #ff6600;
            box-shadow: 0 0 0 3px rgba(255,102,0,0.15);
        }
        .back-link {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.3s;
            display: inline-block;
            margin-top: 1.5rem;
        }
        .back-link:hover {
            color: #ff6600;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.2rem;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <span class="login-icon">🔐</span>
                <h1 class="login-title">Panel de Administrador</h1>
                <p class="login-subtitle">Transparencia Ciudadana</p>
            </div>

            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>❌ Error:</strong>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf

                    <div class="form-floating mb-3">
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               placeholder="nombre@ejemplo.com"
                               value="{{ old('email') }}"
                               required
                               autofocus>
                        <label for="email">📧 Correo Electrónico</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password"
                               class="form-control"
                               id="password"
                               name="password"
                               placeholder="Contraseña"
                               required>
                        <label for="password">🔑 Contraseña</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               id="remember"
                               name="remember">
                        <label class="form-check-label" for="remember">
                            Recordar mi sesión
                        </label>
                    </div>

                    <button type="submit" class="btn btn-login">
                        Iniciar Sesión →
                    </button>
                </form>

                <div class="text-center">
                    <a href="{{ route('home') }}" class="back-link">
                        ← Volver al sitio público
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
