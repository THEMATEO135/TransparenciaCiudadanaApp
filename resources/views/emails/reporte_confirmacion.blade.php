<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Reporte</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #ff6600 0%, #ff8833 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">
            ✅ ¡Reporte Creado Exitosamente!
        </h1>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;">
        <p style="font-size: 16px; margin-bottom: 20px;">
            Hola <strong>{{ $reporte->nombres }}</strong>,
        </p>

        <p style="font-size: 16px; margin-bottom: 20px;">
            Hemos recibido tu reporte exitosamente. Nuestro equipo lo revisará y comenzará a trabajar en él lo antes posible.
        </p>

        <div style="background: white; padding: 25px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ff6600; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; color: #ff6600; font-size: 20px;">
                📋 Tu número de reporte: <span style="font-size: 28px;">#{{ $reporte->id }}</span>
            </h3>
            <p style="margin: 10px 0; color: #666; font-size: 14px;">
                <strong>Guarda este número para dar seguimiento a tu reporte</strong>
            </p>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #666;">📝 Detalles del Reporte</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: bold; width: 40%;">Servicio:</td>
                    <td style="padding: 12px 0;">{{ $reporte->servicio->nombre }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: bold;">Ubicación:</td>
                    <td style="padding: 12px 0;">{{ $reporte->barrio ?? $reporte->localidad ?? 'N/A' }}, {{ $reporte->ciudad->nombre ?? 'N/A' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: bold;">Fecha:</td>
                    <td style="padding: 12px 0;">{{ $reporte->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @if($reporte->proveedor)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: bold;">Proveedor:</td>
                    <td style="padding: 12px 0;">{{ $reporte->proveedor->nombre }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 12px 0; font-weight: bold;">Estado:</td>
                    <td style="padding: 12px 0;">
                        <span style="background: #17a2b8; color: white; padding: 4px 12px; border-radius: 12px; font-size: 14px;">
                            Pendiente
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        @if($reporte->descripcion)
        <div style="background: white; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #666;">Tu descripción:</h4>
            <p style="margin: 0; color: #555; font-style: italic;">"{{ $reporte->descripcion }}"</p>
        </div>
        @endif

        @if($reporte->imagenes && count($reporte->imagenes) > 0)
        <div style="background: white; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #666;">📷 Imágenes adjuntas:</h4>
            <p style="margin: 0; color: #555;">Has adjuntado {{ count($reporte->imagenes) }} imagen(es) como evidencia.</p>
        </div>
        @endif

        @if($reporte->prioridad)
        <div style="background: #{{ $reporte->color_prioridad === 'danger' ? 'ffebee' : ($reporte->color_prioridad === 'warning' ? 'fff3cd' : 'e3f2fd') }}; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #{{ $reporte->color_prioridad === 'danger' ? 'f44336' : ($reporte->color_prioridad === 'warning' ? 'ffc107' : '2196f3') }};">
            <p style="margin: 0; font-size: 15px;">
                <strong>🚩 Prioridad: {{ ucfirst($reporte->prioridad) }}</strong><br>
                Tu reporte ha sido clasificado con prioridad {{ $reporte->prioridad }}.
            </p>
        </div>
        @endif

        <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 30px 0; text-align: center;">
            <h3 style="margin-top: 0; color: #1565c0;">🔔 ¿Qué sigue?</h3>
            <ol style="text-align: left; margin: 15px 0; padding-left: 20px; color: #1565c0;">
                <li style="margin-bottom: 10px;">Nuestro equipo revisará tu reporte</li>
                <li style="margin-bottom: 10px;">Se lo asignaremos a un operador especializado</li>
                <li style="margin-bottom: 10px;">Comenzarán a trabajar en la solución</li>
                <li style="margin-bottom: 10px;">Te notificaremos sobre cualquier actualización</li>
                <li>Cuando esté resuelto, te pediremos tu opinión</li>
            </ol>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('reportes.timeline', $reporte->id) }}"
               style="display: inline-block; background: #ff6600; color: white; padding: 15px 40px; text-decoration: none; border-radius: 50px; font-size: 18px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 10px;">
                🕒 Ver Timeline de tu Reporte
            </a>
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ route('reportes.historial') }}"
               style="display: inline-block; background: white; color: #ff6600; padding: 10px 30px; text-decoration: none; border-radius: 25px; font-size: 14px; border: 2px solid #ff6600;">
                📋 Ver Todos Mis Reportes
            </a>
        </div>

        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">

        <div style="background: #f5f5f5; padding: 15px; border-radius: 8px;">
            <h4 style="margin-top: 0; color: #666;">📌 Información Importante:</h4>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">Recibirás notificaciones por correo cada vez que haya actualizaciones</li>
                <li style="margin-bottom: 8px;">Puedes ver el progreso en tiempo real en el timeline</li>
                <li style="margin-bottom: 8px;">Puedes agregar comentarios o información adicional en cualquier momento</li>
                <li style="margin-bottom: 8px;">El tiempo de resolución depende de la complejidad y prioridad del reporte</li>
            </ul>
        </div>

        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center;">
            <p style="margin: 0; font-size: 14px; color: #856404;">
                <strong>💡 Tip:</strong> Guarda el correo con tu número de reporte (#{{ $reporte->id }}) para consultarlo fácilmente.
            </p>
        </div>

        <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin-top: 30px; text-align: center;">
            <p style="margin: 0; font-size: 14px; color: #2e7d32;">
                <strong>¡Gracias por usar nuestro sistema!</strong><br>
                Tu reporte nos ayuda a mejorar los servicios para toda la comunidad.
            </p>
        </div>
    </div>

    <div style="text-align: center; padding: 20px; color: #999; font-size: 12px;">
        <p style="margin: 5px 0;">
            ¿Necesitas ayuda? Visita nuestro sitio web
        </p>
        <p style="margin: 5px 0;">
            Este es un correo automático, por favor no respondas a este mensaje.
        </p>
        <p style="margin: 5px 0;">
            © {{ date('Y') }} Sistema de Transparencia Ciudadana
        </p>
    </div>
</body>
</html>
