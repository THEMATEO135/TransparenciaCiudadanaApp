# Comandos Útiles - Sistema de Transparencia Ciudadana

## 🚀 Iniciar el Sistema

### Desarrollo Local

```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Queue Worker (procesar webhooks)
php artisan queue:work

# Terminal 3: Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

### Desarrollo con npm/vite (si aplica)
```bash
# Terminal adicional: Compilar assets
npm run dev
```

## 🔧 Gestión del Queue Worker

### Iniciar el worker
```bash
php artisan queue:work
```

### Iniciar con opciones específicas
```bash
# Con reintentos y timeout
php artisan queue:work --tries=3 --timeout=60

# Solo procesar un job y detenerse
php artisan queue:work --once

# Ver qué está haciendo el worker
php artisan queue:work --verbose
```

### Reiniciar workers (después de cambios en código)
```bash
php artisan queue:restart
```

### Ver jobs fallidos
```bash
php artisan queue:failed

# Reintentar un job fallido
php artisan queue:retry [job-id]

# Reintentar todos los jobs fallidos
php artisan queue:retry all

# Limpiar jobs fallidos
php artisan queue:flush
```

### Monitorear la cola
```bash
# Ver estadísticas de la cola
php artisan queue:monitor

# Ver jobs en espera
php artisan queue:work --stop-when-empty
```

## 🧪 Probar Webhooks

### Probar desde navegador
```
http://localhost:8000/test-webhook
```

### Probar desde Artisan Tinker
```bash
php artisan tinker

# Dentro de tinker:
$payload = [
    'reporte_id' => 999,
    'nombres' => 'Test User',
    'correo' => 'test@example.com',
    'telefono' => '+573001234567',
    'descripcion' => 'Prueba',
    'estado' => 'pendiente',
    'prioridad' => 'alta',
    'servicio' => ['id' => 1, 'nombre' => 'Test']
];

\App\Jobs\SendReportToN8n::dispatch($payload, 'reporte_nuevo');

// Ver el resultado
exit
```

### Probar con cURL
```bash
# Crear un reporte de prueba (esto disparará el webhook automáticamente)
curl -X POST http://localhost:8000/reportes \
  -H "Content-Type: application/json" \
  -d '{
    "nombres": "Juan Test",
    "correo": "test@example.com",
    "telefono": "+573001234567",
    "servicio_id": 1,
    "descripcion": "Prueba de webhook",
    "direccion": "Calle 1 # 2-3"
  }'
```

## 📊 Logs y Debugging

### Ver logs en tiempo real
```bash
# Todos los logs
tail -f storage/logs/laravel.log

# Solo errores
tail -f storage/logs/laravel.log | grep ERROR

# Solo webhooks
tail -f storage/logs/laravel.log | grep webhook

# Solo jobs de n8n
tail -f storage/logs/laravel.log | grep SendReportToN8n
```

### Limpiar logs antiguos
```bash
# Vaciar el log actual
> storage/logs/laravel.log

# O eliminar y recrear
rm storage/logs/laravel.log
touch storage/logs/laravel.log
chmod 666 storage/logs/laravel.log
```

### Ver últimas líneas del log
```bash
# Últimas 100 líneas
tail -n 100 storage/logs/laravel.log

# Últimas 50 líneas con errores
tail -n 100 storage/logs/laravel.log | grep -i error
```

## 🗄️ Base de Datos

### Ver reportes recientes
```bash
php artisan tinker

# Dentro de tinker:
\App\Models\Reporte::latest()->take(5)->get(['id', 'nombres', 'estado', 'created_at']);
```

### Verificar configuración
```bash
# Ver variables de entorno
php artisan config:show

# Verificar conexión a BD
php artisan db:show

# Ver tablas
php artisan db:table --table=reportes
```

### Migraciones (si necesitas resetear)
```bash
# Ver estado de migraciones
php artisan migrate:status

# Ejecutar migraciones pendientes
php artisan migrate

# Rollback última migración
php artisan migrate:rollback

# CUIDADO: Resetear toda la BD
php artisan migrate:fresh --seed
```

## 🔄 Caché

### Limpiar caché
```bash
# Limpiar toda la caché
php artisan cache:clear

# Limpiar caché de configuración
php artisan config:clear

# Limpiar caché de rutas
php artisan route:clear

# Limpiar caché de vistas
php artisan view:clear

# Limpiar todo de una vez
php artisan optimize:clear
```

### Optimizar para producción
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 📧 Testing de Emails

### Ver emails en log (desarrollo)
```bash
# Cambiar en .env:
MAIL_MAILER=log

# Los emails se guardarán en storage/logs/laravel.log
tail -f storage/logs/laravel.log | grep "Message-ID:"
```

### Probar envío de email
```bash
php artisan tinker

# Dentro de tinker:
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')
            ->subject('Test');
});
```

## 🔐 Seguridad

### Regenerar APP_KEY (solo si es necesario)
```bash
php artisan key:generate
```

### Listar rutas protegidas
```bash
php artisan route:list --path=admin
```

### Ver usuarios admin
```bash
php artisan tinker

# Dentro de tinker:
\App\Models\User::where('role', 'admin')->get(['id', 'name', 'email']);
```

## 🌐 n8n en Railway

### Ver logs de n8n
```
# Accede a Railway dashboard:
https://railway.app/

# Selecciona tu proyecto
# Ve a la pestaña "Deployments"
# Haz clic en "View Logs"
```

### Reiniciar n8n
```
# En Railway dashboard:
# Selecciona el servicio de n8n
# Haz clic en "Settings"
# Haz clic en "Restart"
```

### Ver webhook URL
```bash
# Desde Laravel:
php artisan tinker

# Dentro de tinker:
env('WEBHOOK_URL');
```

## 📦 Mantenimiento

### Actualizar dependencias
```bash
# Composer
composer update

# NPM
npm update

# Verificar vulnerabilidades
npm audit
```

### Limpiar archivos temporales
```bash
# Limpiar storage
php artisan storage:link

# Eliminar archivos de caché antiguos
find storage/framework/cache -type f -mtime +7 -delete
```

## 🐛 Troubleshooting

### El webhook no funciona
```bash
# 1. Verificar que la URL esté configurada
php artisan tinker
env('WEBHOOK_URL');

# 2. Verificar que el queue worker esté corriendo
ps aux | grep "queue:work"

# 3. Ver jobs fallidos
php artisan queue:failed

# 4. Ver últimos logs
tail -n 50 storage/logs/laravel.log
```

### Permisos de archivos
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows: Ejecutar como administrador
icacls storage /grant Users:F /t
icacls bootstrap/cache /grant Users:F /t
```

### Limpiar todo y empezar de nuevo
```bash
php artisan optimize:clear
php artisan cache:clear
php artisan queue:restart
composer dump-autoload
php artisan config:cache
```

## 📱 Producción con Supervisor (Linux)

### Instalar Supervisor
```bash
sudo apt-get install supervisor
```

### Configurar worker
```bash
# Crear archivo: /etc/supervisor/conf.d/laravel-worker.conf
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta/completa/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/ruta/completa/storage/logs/worker.log
stopwaitsecs=3600

# Recargar configuración
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Comandos Supervisor
```bash
# Ver estado
sudo supervisorctl status

# Iniciar workers
sudo supervisorctl start laravel-worker:*

# Detener workers
sudo supervisorctl stop laravel-worker:*

# Reiniciar workers
sudo supervisorctl restart laravel-worker:*

# Ver logs
sudo tail -f /ruta/completa/storage/logs/worker.log
```

## 🎯 Comandos Rápidos Útiles

```bash
# Ver versión de Laravel
php artisan --version

# Listar todos los comandos artisan
php artisan list

# Ver ayuda de un comando específico
php artisan help queue:work

# Ejecutar un comando específico del proyecto
php artisan app:calculate-priorities
php artisan app:detect-duplicates
php artisan app:generate-predictions

# Ver información del sistema
php artisan about

# Modo mantenimiento
php artisan down --message="Estamos en mantenimiento"
php artisan up
```
