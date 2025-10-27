<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Feedback</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #ff6600 0%, #ff8833 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">
            ✅ ¡Tu reporte ha sido resuelto!
        </h1>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;">
        <p style="font-size: 16px; margin-bottom: 20px;">
            Hola <strong>{{ $reporte->nombres }}</strong>,
        </p>

        <p style="font-size: 16px; margin-bottom: 20px;">
            Nos complace informarte que tu reporte <strong>#{{ $reporte->id }}</strong> sobre
            <strong>{{ $reporte->servicio->nombre }}</strong> ha sido marcado como resuelto.
        </p>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ff6600;">
            <h3 style="margin-top: 0; color: #ff6600;">📋 Detalles del Reporte</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; width: 40%;">Servicio:</td>
                    <td style="padding: 8px 0;">{{ $reporte->servicio->nombre }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Ubicación:</td>
                    <td style="padding: 8px 0;">{{ $reporte->barrio ?? $reporte->localidad ?? 'N/A' }}, {{ $reporte->ciudad->nombre ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Fecha del reporte:</td>
                    <td style="padding: 8px 0;">{{ $reporte->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Proveedor:</td>
                    <td style="padding: 8px 0;">{{ $reporte->proveedor->nombre ?? 'No especificado' }}</td>
                </tr>
            </table>
        </div>

        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">
            <p style="margin: 0; font-size: 15px;">
                <strong>💬 Tu opinión es muy importante para nosotros</strong><br>
                Por favor, tómate un momento para calificar el servicio recibido y ayúdanos a mejorar.
            </p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('feedback.mostrar', $feedback->token) }}"
               style="display: inline-block; background: #ff6600; color: white; padding: 15px 40px; text-decoration: none; border-radius: 50px; font-size: 18px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                📝 Dar Feedback
            </a>
        </div>

        <p style="font-size: 14px; color: #666; text-align: center; margin-top: 20px;">
            Este enlace estará activo por <strong>7 días</strong>
        </p>

        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">

        <p style="font-size: 14px; color: #666; margin: 10px 0;">
            Si tienes alguna pregunta o el problema no está realmente resuelto, por favor háznos saber en el formulario de feedback.
        </p>

        <p style="font-size: 14px; color: #666; margin: 10px 0;">
            También puedes ver el historial completo de tu reporte aquí:
        </p>

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ route('reportes.timeline', $reporte->id) }}"
               style="display: inline-block; background: white; color: #ff6600; padding: 10px 30px; text-decoration: none; border-radius: 25px; font-size: 14px; border: 2px solid #ff6600;">
                🕒 Ver Timeline del Reporte
            </a>
        </div>

        <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin-top: 30px; text-align: center;">
            <p style="margin: 0; font-size: 14px; color: #2e7d32;">
                <strong>¡Gracias por usar nuestro sistema de reportes ciudadanos!</strong><br>
                Tu participación nos ayuda a mejorar los servicios para toda la comunidad.
            </p>
        </div>
    </div>

    <div style="text-align: center; padding: 20px; color: #999; font-size: 12px;">
        <p style="margin: 5px 0;">
            Este es un correo automático, por favor no respondas a este mensaje.
        </p>
        <p style="margin: 5px 0;">
            © {{ date('Y') }} Sistema de Transparencia Ciudadana
        </p>
    </div>
</body>
</html>
