# ✅ Checklist de Configuración - Integración n8n

Use este checklist para verificar que la integración con n8n esté correctamente configurada.

## 📋 Configuración Laravel

### ✅ Archivo .env
- [ ] Variable `WEBHOOK_URL` existe y tiene la URL correcta
  ```bash
  grep WEBHOOK_URL .env
  # Debe mostrar: WEBHOOK_URL=https://primary-production-0845.up.railway.app/webhook-test/9bf08527-7c30-4680-be8a-fd53c4c1a124
  ```

- [ ] Configuración de email (Gmail) está correcta
  ```bash
  grep MAIL_ .env
  # Verificar:
  # MAIL_HOST=smtp.gmail.com
  # MAIL_PORT=587
  # MAIL_USERNAME=themateo135@gmail.com
  # MAIL_PASSWORD=smywavzvsxuqksrv
  ```

- [ ] Configuración de queue está en database
  ```bash
  grep QUEUE_CONNECTION .env
  # Debe mostrar: QUEUE_CONNECTION=database
  ```

### ✅ Archivos del sistema
- [ ] Job `SendReportToN8n.php` existe y está actualizado
  ```bash
  ls -l app/Jobs/SendReportToN8n.php
  ```

- [ ] ReporteController tiene método `testWebhook()`
  ```bash
  grep -n "testWebhook" app/Http/Controllers/ReporteController.php
  ```

- [ ] Ruta de prueba existe en `routes/web.php`
  ```bash
  grep "test-webhook" routes/web.php
  ```

### ✅ Base de datos
- [ ] Tabla `jobs` existe
  ```bash
  php artisan db:table jobs
  ```

- [ ] Tabla `failed_jobs` existe
  ```bash
  php artisan db:table failed_jobs
  ```

## 🎯 n8n en Railway

### ✅ Acceso a n8n
- [ ] Puedo acceder a n8n en el navegador
  - URL: https://primary-production-0845.up.railway.app
  - [ ] Usuario y contraseña funcionan

### ✅ Workflow importado
- [ ] El workflow "Transparencia Ciudadana - Notificaciones" está importado
- [ ] El workflow está ACTIVO (toggle en verde)
- [ ] El webhook está configurado con la ruta correcta:
  - `webhook-test/9bf08527-7c30-4680-be8a-fd53c4c1a124`

### ✅ Nodos configurados

#### Webhook Node
- [ ] Path: `webhook-test/9bf08527-7c30-4680-be8a-fd53c4c1a124`
- [ ] HTTP Method: POST
- [ ] Response Mode: Response Node

#### Email Nodes
Para cada nodo de email (4 nodos):
- [ ] SMTP Host: smtp.gmail.com
- [ ] Puerto: 587
- [ ] Usuario: themateo135@gmail.com
- [ ] Contraseña: configurada
- [ ] Sender Name: Transparencia Ciudadana

#### WhatsApp Nodes (Opcional)
- [ ] Configurados con Twilio o WhatsApp Business API
- [ ] O desactivados si no se usan aún

## 🧪 Pruebas

### ✅ Prueba 1: Queue Worker
1. Iniciar el worker:
   ```bash
   php artisan queue:work
   ```
   - [ ] El comando inicia sin errores
   - [ ] Muestra: "Processing jobs from the [default] queue"

### ✅ Prueba 2: Webhook de Test
1. En otra terminal o navegador, acceder a:
   ```
   http://localhost:8000/test-webhook
   ```
   - [ ] Responde con JSON exitoso
   - [ ] En la terminal del queue worker aparece: "Processing: App\Jobs\SendReportToN8n"
   - [ ] El job se completa sin errores

2. En n8n:
   - [ ] Ve a "Executions" en el menú lateral
   - [ ] La última ejecución muestra éxito (verde)
   - [ ] Puedes ver los datos recibidos

3. En logs de Laravel:
   ```bash
   tail -n 50 storage/logs/laravel.log | grep webhook
   ```
   - [ ] Aparecen logs de "Enviando webhook a n8n"
   - [ ] Aparecen logs de "Webhook enviado exitosamente a n8n"

### ✅ Prueba 3: Crear Reporte Real
1. Acceder al formulario de creación de reportes
2. Llenar todos los campos obligatorios
3. Enviar el reporte
   - [ ] El reporte se crea exitosamente
   - [ ] En el queue worker se procesa el job
   - [ ] En n8n aparece una nueva ejecución
   - [ ] Se recibe un email en la cuenta del ciudadano

### ✅ Prueba 4: Actualizar Estado
1. Como admin, cambiar el estado de un reporte
   - [ ] El cambio se guarda
   - [ ] Se dispara el webhook
   - [ ] Se recibe email de notificación

### ✅ Prueba 5: Agregar Comentario
1. Como ciudadano, agregar un comentario a un reporte
   - [ ] El comentario se guarda
   - [ ] Se dispara el webhook
   - [ ] Los admins reciben notificación

### ✅ Prueba 6: Enviar Feedback
1. Acceder al link de feedback de un reporte cerrado
2. Responder la encuesta
   - [ ] El feedback se guarda
   - [ ] Se dispara el webhook
   - [ ] Los admins reciben notificación

## 📊 Verificación de Logs

### ✅ Logs de Laravel
```bash
tail -f storage/logs/laravel.log
```
Buscar:
- [ ] "Enviando webhook a n8n"
- [ ] "Webhook enviado exitosamente a n8n"
- [ ] No hay errores de "Error enviando webhook"

### ✅ Logs de n8n
En Railway dashboard:
- [ ] No hay errores 500
- [ ] Las ejecuciones muestran estado success
- [ ] Los payloads recibidos tienen la estructura correcta

### ✅ Emails enviados
Verificar:
- [ ] Los emails llegan a la bandeja de entrada (no spam)
- [ ] El contenido del email es correcto
- [ ] Las variables se reemplazan correctamente (no aparecen {{$json.campo}})

## 🚨 Troubleshooting

### Si el webhook no funciona:

1. **Verificar WEBHOOK_URL**
   ```bash
   php artisan tinker
   env('WEBHOOK_URL');
   ```
   - [ ] La URL es correcta

2. **Verificar queue worker**
   ```bash
   ps aux | grep "queue:work"
   ```
   - [ ] El proceso está corriendo

3. **Verificar jobs fallidos**
   ```bash
   php artisan queue:failed
   ```
   - [ ] No hay jobs fallidos
   - Si hay, revisar el error y corregir

4. **Verificar conectividad**
   ```bash
   curl -X POST https://primary-production-0845.up.railway.app/webhook-test/9bf08527-7c30-4680-be8a-fd53c4c1a124 \
     -H "Content-Type: application/json" \
     -d '{"test": true}'
   ```
   - [ ] Responde con éxito

### Si los emails no llegan:

1. **Verificar credenciales de Gmail**
   - [ ] La contraseña de aplicación es válida
   - [ ] "Acceso a aplicaciones menos seguras" está habilitado

2. **Verificar en spam**
   - [ ] Revisar carpeta de spam del destinatario

3. **Verificar logs de n8n**
   - [ ] El nodo de email se ejecuta sin errores

## ✅ Configuración de Producción

Antes de llevar a producción:

### Supervisor/PM2
- [ ] Configurado para mantener el queue worker corriendo
- [ ] Reinicia automáticamente si falla
- [ ] Logs se guardan correctamente

### Performance
- [ ] Cache de configuración: `php artisan config:cache`
- [ ] Cache de rutas: `php artisan route:cache`
- [ ] Optimizaciones: `php artisan optimize`

### Seguridad
- [ ] APP_DEBUG=false en producción
- [ ] APP_ENV=production
- [ ] Rutas de test deshabilitadas o protegidas

### Monitoreo
- [ ] Configurado sistema de alertas para jobs fallidos
- [ ] Logs se revisan periódicamente
- [ ] Métricas de n8n se monitorean

## 📝 Notas Finales

Después de completar este checklist:
- Fecha de verificación: _______________
- Verificado por: _______________
- Problemas encontrados: _______________
- Soluciones aplicadas: _______________

---

**Estado de la integración:**
- [ ] ✅ Todo funcionando correctamente
- [ ] ⚠️ Funcionando con advertencias menores
- [ ] ❌ Requiere correcciones
