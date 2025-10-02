<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #ff6600, #e55a00); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .code { font-size: 36px; font-weight: bold; color: #ff6600; text-align: center; letter-spacing: 10px; padding: 20px; background: white; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; color: #718096; margin-top: 20px; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">Transparencia Ciudadana</h1>
            <p style="margin: 10px 0 0;">Código de Verificación</p>
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>Has solicitado acceder a tu historial de reportes. Utiliza el siguiente código para verificar tu identidad:</p>
            <div class="code">{{ $code }}</div>
            <p><strong>Este código expira en 10 minutos.</strong></p>
            <p>Si no solicitaste este código, puedes ignorar este mensaje.</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Transparencia Ciudadana - COMPENSAR</p>
        </div>
    </div>
</body>
</html>
