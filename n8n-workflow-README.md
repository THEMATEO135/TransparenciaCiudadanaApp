# Configuración del Workflow de n8n - Transparencia Ciudadana

Este documento explica cómo importar y configurar el workflow de n8n para recibir notificaciones de la aplicación de Transparencia Ciudadana.

## Características del Workflow

El workflow procesa 4 tipos de eventos:
- ✅ **Reporte Nuevo**: Cuando un ciudadano crea un reporte
- 🔄 **Actualización de Estado**: Cuando un admin cambia el estado de un reporte
- 💬 **Comentario**: Cuando se agrega un comentario al reporte
- ⭐ **Feedback**: Cuando un ciudadano responde la encuesta de satisfacción

Para cada evento, el workflow:
1. Recibe el webhook desde Laravel
2. Registra los datos en logs
3. Envía un email al ciudadano (o admin según el caso)
4. Envía una notificación por WhatsApp
5. Responde al webhook confirmando la recepción

## 📥 Importar el Workflow en n8n

### Paso 1: Abrir n8n
1. Accede a tu instancia de n8n en Railway: `https://primary-production-0845.up.railway.app`
2. Inicia sesión con tus credenciales

### Paso 2: Importar el JSON
1. En n8n, haz clic en el menú hamburguesa (☰) en la esquina superior izquierda
2. Selecciona "Import from File" o "Import workflow"
3. Selecciona el archivo `n8n-workflow-transparencia.json`
4. Haz clic en "Import"

### Paso 3: Verificar el Webhook
1. Abre el nodo "Webhook" (primer nodo del workflow)
2. Verifica que la ruta sea: `webhook-test/9bf08527-7c30-4680-be8a-fd53c4c1a124`
3. El método debe ser: `POST`
4. Haz clic en "Execute Node" para activar el webhook

## 📧 Configuración de Email

Los nodos de email ya están preconfigurados con tus credenciales de Gmail:
- **SMTP Host**: smtp.gmail.com
- **Puerto**: 587
- **Usuario**: themateo135@gmail.com
- **Contraseña**: Ya configurada (smywavzvsxuqksrv)

**No necesitas cambiar nada en los nodos de email**, pero asegúrate de que:
1. La contraseña de aplicación de Gmail siga siendo válida
2. El email de origen esté autorizado para enviar desde Gmail

## 📱 Configuración de WhatsApp

Los nodos de WhatsApp están preconfigurados como placeholders. Tienes 3 opciones para activarlos:

### Opción 1: Twilio (Recomendado)

1. **Crear cuenta en Twilio**:
   - Visita: https://www.twilio.com/try-twilio
   - Regístrate y verifica tu cuenta
   - Obtén un número de teléfono con capacidad de WhatsApp

2. **Configurar cada nodo de WhatsApp**:
   ```
   Método: POST
   URL: https://api.twilio.com/2010-04-01/Accounts/{{ACCOUNT_SID}}/Messages.json
   Authentication: Basic Auth
   - User: Tu ACCOUNT_SID de Twilio
   - Password: Tu AUTH_TOKEN de Twilio

   Body (Form):
   - From: whatsapp:+14155238886 (número de Twilio)
   - To: whatsapp:{{$json.telefono}}
   - Body: {{mensaje del template}}
   ```

### Opción 2: WhatsApp Business API

1. **Obtener acceso a WhatsApp Business API**:
   - Regístrate en Meta Business: https://business.facebook.com
   - Solicita acceso a WhatsApp Business API
   - Obtén tu número de teléfono empresarial

2. **Configurar los nodos**:
   ```
   Método: POST
   URL: https://graph.facebook.com/v18.0/{{PHONE_NUMBER_ID}}/messages
   Authentication: Header Auth
   - Header: Authorization
   - Value: Bearer {{ACCESS_TOKEN}}

   Body (JSON):
   {
     "messaging_product": "whatsapp",
     "to": "{{$json.telefono}}",
     "type": "text",
     "text": {
       "body": "{{mensaje}}"
     }
   }
   ```

### Opción 3: Desactivar WhatsApp

Si no quieres usar WhatsApp por ahora:
1. Selecciona cada nodo de WhatsApp
2. Haz clic derecho
3. Selecciona "Disable"
4. El workflow solo enviará emails

## 🧪 Probar el Workflow

### 1. Activar el Workflow
1. En n8n, abre el workflow importado
2. Haz clic en el botón "Active" en la esquina superior derecha
3. El workflow ahora está escuchando webhooks

### 2. Probar desde Laravel
```bash
# En tu terminal, ejecuta:
php artisan queue:work

# En otra terminal o navegador, accede a:
# http://tu-dominio.local/test-webhook
```

O desde tu código Laravel:
```php
// Crear ruta de prueba en routes/web.php
Route::get('/test-webhook', function() {
    $payload = [
        'test' => true,
        'reporte_id' => 999,
        'nombres' => 'Juan Pérez',
        'correo' => 'test@example.com',
        'telefono' => '+573001234567',
        'descripcion' => 'Prueba de webhook',
        'estado' => 'pendiente',
        'prioridad' => 'alta',
        'servicio' => [
            'id' => 1,
            'nombre' => 'Servicio de Prueba'
        ]
    ];

    \App\Jobs\SendReportToN8n::dispatch($payload, 'reporte_nuevo');

    return 'Webhook de prueba enviado';
});
```

### 3. Verificar en n8n
1. Ve a n8n → Executions (en el menú lateral)
2. Deberías ver la ejecución del workflow
3. Haz clic en ella para ver los detalles y logs
4. Verifica que cada nodo se ejecutó correctamente

## 📊 Monitoreo y Logs

### Ver Logs en n8n
1. Ve a "Executions" en el menú lateral
2. Haz clic en cualquier ejecución para ver detalles
3. El nodo "Log Datos" muestra información detallada en la consola

### Ver Logs en Laravel
```bash
# En terminal
tail -f storage/logs/laravel.log

# Buscar entradas relacionadas con webhooks:
grep "webhook" storage/logs/laravel.log
grep "SendReportToN8n" storage/logs/laravel.log
```

## 🔧 Personalización del Workflow

### Cambiar Plantillas de Email
1. Abre cualquier nodo "Email - [Tipo]"
2. Modifica el campo "Text" con tu mensaje personalizado
3. Puedes usar variables: `{{$json.campo}}`

### Cambiar Plantillas de WhatsApp
1. Abre cualquier nodo "WhatsApp - [Tipo]"
2. Modifica el campo "body" en el Body
3. Mantén los mensajes cortos para WhatsApp

### Agregar Nuevas Notificaciones
1. Arrastra un nuevo nodo desde el panel lateral
2. Conéctalo después del email o WhatsApp
3. Configura el nodo según tus necesidades

## 🚨 Troubleshooting

### El webhook no recibe datos
- Verifica que `WEBHOOK_URL` en `.env` sea correcta
- Asegúrate de que el workflow esté ACTIVO en n8n
- Verifica que `php artisan queue:work` esté corriendo
- Revisa los logs de Laravel

### Los emails no se envían
- Verifica la contraseña de aplicación de Gmail
- Asegúrate de que "Acceso a aplicaciones menos seguras" esté habilitado
- Revisa los logs del nodo de email en n8n

### WhatsApp no funciona
- Verifica las credenciales de Twilio/WhatsApp Business
- Asegúrate de que el formato del número sea correcto: `+[código país][número]`
- Revisa los logs de Twilio/Meta

### El queue worker se detiene
En producción, usa Supervisor o PM2:

**Supervisor (Linux)**:
```bash
sudo apt-get install supervisor

# Crear archivo /etc/supervisor/conf.d/laravel-worker.conf
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/laravel-worker.log
```

## 📞 Soporte

Si tienes problemas:
1. Revisa los logs de Laravel: `storage/logs/laravel.log`
2. Revisa las ejecuciones en n8n
3. Verifica la configuración de cada nodo
4. Prueba con el endpoint de test primero

## 🎯 Próximos Pasos

1. ✅ Importar y activar el workflow
2. ✅ Probar con el endpoint de test
3. ✅ Configurar WhatsApp (opcional)
4. ✅ Personalizar plantillas de mensajes
5. ✅ Configurar supervisor/PM2 para producción
6. ✅ Monitorear las primeras ejecuciones reales
