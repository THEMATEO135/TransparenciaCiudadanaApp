<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de tu Reporte</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #ff6600 0%, #ff8833 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">
            @if($tipo === 'recibido')
            📬 Reporte Recibido
            @elseif($tipo === 'asignado')
            👤 Reporte Asignado
            @elseif($tipo === 'en_proceso')
            ⚙️ En Proceso
            @elseif($tipo === 'requiere_informacion')
            ❓ Información Requerida
            @elseif($tipo === 'resuelto')
            ✅ Reporte Resuelto
            @else
            🔔 Actualización de Reporte
            @endif
        </h1>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;">
        <p style="font-size: 16px; margin-bottom: 20px;">
            Hola <strong>{{ $reporte->nombres }}</strong>,
        </p>

        @if($tipo === 'recibido')
        <p style="font-size: 16px; margin-bottom: 20px;">
            Hemos recibido tu reporte exitosamente. A continuación los detalles:
        </p>
        @elseif($tipo === 'asignado')
        <p style="font-size: 16px; margin-bottom: 20px;">
            Tu reporte ha sido asignado a un operador quien se encargará de atenderlo lo antes posible.
        </p>
        @elseif($tipo === 'en_proceso')
        <p style="font-size: 16px; margin-bottom: 20px;">
            Tu reporte está siendo atendido. Nuestro equipo está trabajando para resolver el problema.
        </p>
        @elseif($tipo === 'requiere_informacion')
        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">
            <p style="margin: 0; font-size: 15px;">
                <strong>⚠️ Necesitamos más información</strong><br>
                Para poder resolver tu reporte, necesitamos que nos proporciones información adicional.
            </p>
        </div>
        @elseif($tipo === 'resuelto')
        <p style="font-size: 16px; margin-bottom: 20px;">
            ¡Buenas noticias! Tu reporte ha sido marcado como resuelto.
        </p>
        @else
        <p style="font-size: 16px; margin-bottom: 20px;">
            Hay una nueva actualización en tu reporte.
        </p>
        @endif

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ff6600;">
            <h3 style="margin-top: 0; color: #ff6600;">📋 Reporte #{{ $reporte->id }}</h3>
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
                    <td style="padding: 8px 0; font-weight: bold;">Estado:</td>
                    <td style="padding: 8px 0;">
                        <span style="background: #{{ $reporte->color_estado }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 14px;">
                            {{ ucfirst(str_replace('_', ' ', $reporte->estado)) }}
                        </span>
                    </td>
                </tr>
                @if($reporte->prioridad)
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Prioridad:</td>
                    <td style="padding: 8px 0;">
                        <span style="background: #{{ $reporte->color_prioridad }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 14px;">
                            {{ ucfirst($reporte->prioridad) }}
                        </span>
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Fecha del reporte:</td>
                    <td style="padding: 8px 0;">{{ $reporte->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @if($reporte->proveedor)
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Proveedor:</td>
                    <td style="padding: 8px 0;">{{ $reporte->proveedor->nombre }}</td>
                </tr>
                @endif
            </table>
        </div>

        @if($reporte->descripcion)
        <div style="background: white; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #666;">Descripción:</h4>
            <p style="margin: 0;">{{ $reporte->descripcion }}</p>
        </div>
        @endif

        @if($tipo === 'requiere_informacion' && isset($mensaje))
        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #856404;">Información solicitada:</h4>
            <p style="margin: 0; color: #856404;">{{ $mensaje }}</p>
        </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('reportes.timeline', $reporte->id) }}"
               style="display: inline-block; background: #ff6600; color: white; padding: 15px 40px; text-decoration: none; border-radius: 50px; font-size: 18px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                🕒 Ver Timeline Completo
            </a>
        </div>

        @if($tipo === 'requiere_informacion')
        <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center;">
            <p style="margin: 0; font-size: 15px; color: #1565c0;">
                <strong>💬 ¿Necesitas agregar información?</strong><br>
                Puedes responder directamente en el timeline usando el formulario de comentarios.
            </p>
        </div>
        @endif

        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">

        <div style="background: #f5f5f5; padding: 15px; border-radius: 8px;">
            <h4 style="margin-top: 0; color: #666;">📌 Recordatorios:</h4>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">Puedes ver todas las actualizaciones en el timeline de tu reporte</li>
                <li style="margin-bottom: 8px;">Recibirás notificaciones cada vez que haya cambios importantes</li>
                <li style="margin-bottom: 8px;">Puedes agregar comentarios o información adicional cuando lo necesites</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ route('reportes.historial') }}"
               style="display: inline-block; background: white; color: #ff6600; padding: 10px 30px; text-decoration: none; border-radius: 25px; font-size: 14px; border: 2px solid #ff6600;">
                📋 Ver Todos Mis Reportes
            </a>
        </div>

        <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin-top: 30px; text-align: center;">
            <p style="margin: 0; font-size: 14px; color: #2e7d32;">
                <strong>¡Gracias por tu paciencia!</strong><br>
                Estamos trabajando para resolver tu reporte lo antes posible.
            </p>
        </div>
    </div>

    <div style="text-align: center; padding: 20px; color: #999; font-size: 12px;">
        <p style="margin: 5px 0;">
            Este es un correo automático, por favor no respondas a este mensaje.
        </p>
        <p style="margin: 5px 0;">
            Para agregar comentarios o información, usa el timeline del reporte.
        </p>
        <p style="margin: 5px 0;">
            © {{ date('Y') }} Sistema de Transparencia Ciudadana
        </p>
    </div>
</body>
</html>
