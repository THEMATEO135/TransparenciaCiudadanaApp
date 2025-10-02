# Guía de Deployment - TransparenciaCiudadanaApp

## Configuración de Producción

### 1. Configurar Variables de Entorno

Asegúrate de configurar estas variables en tu archivo `.env` de producción:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Base de datos (usar MySQL o PostgreSQL en producción)
DB_CONNECTION=mysql
DB_HOST=tu-host-db
DB_PORT=3306
DB_DATABASE=transparencia_db
DB_USERNAME=tu-usuario
DB_PASSWORD=tu-password-seguro

# Cache y Queue (recomendado Redis en producción)
CACHE_STORE=redis
QUEUE_CONNECTION=redis

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tu-dominio.com
MAIL_FROM_NAME="Transparencia Ciudadana"

# Webhook
WEBHOOK_URL=https://tu-webhook-url.com/webhook
```

### 2. Instalar Dependencias

```bash
composer install --optimize-autoloader --no-dev
npm install
```

### 3. Compilar Assets para Producción

```bash
npm run build
```

Esto minificará automáticamente:
- CSS (eliminará espacios y comentarios)
- JavaScript (eliminará console.log, debugger y minificará código)

Los archivos compilados estarán en `public/build/`

### 4. Optimizar Laravel

```bash
# Generar app key si no existe
php artisan key:generate

# Ejecutar migraciones
php artisan migrate --force

# Optimizar configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlaces simbólicos para storage
php artisan storage:link
```

### 5. Configurar Queue Worker

Para que los emails se envíen en segundo plano, necesitas ejecutar el queue worker:

**Opción A: Supervisor (Recomendado para servidores Linux)**

Crear archivo `/etc/supervisor/conf.d/transparencia-worker.conf`:

```ini
[program:transparencia-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta/a/tu/proyecto/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=tu-usuario
numprocs=2
redirect_stderr=true
stdout_logfile=/ruta/a/tu/proyecto/storage/logs/worker.log
stopwaitsecs=3600
```

Luego ejecutar:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start transparencia-worker:*
```

**Opción B: Cron Job (Alternativa)**

Agregar a crontab:
```bash
* * * * * cd /ruta/a/tu/proyecto && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

### 6. Configurar Cache

Si usas Redis en producción:

```bash
# Limpiar cache antigua
php artisan cache:clear

# Verificar conexión Redis
php artisan tinker
>>> Cache::put('test', 'valor', 60);
>>> Cache::get('test');
```

### 7. Seguridad

**IMPORTANTE antes de deployment:**

1. Cambiar contraseña del admin por defecto
2. Asegurar que `APP_DEBUG=false` en producción
3. Configurar HTTPS/SSL
4. Habilitar rate limiting (ya configurado en rutas)
5. Verificar permisos de archivos:
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

### 8. Monitoreo de Queue

Para verificar que los jobs se están procesando:

```bash
# Ver jobs pendientes
php artisan queue:monitor database

# Ver jobs fallidos
php artisan queue:failed

# Reintentar jobs fallidos
php artisan queue:retry all
```

### 9. Limpiar Cache en Producción

Si necesitas limpiar cache después de cambios:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Luego volver a cachear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 10. Verificación Post-Deployment

- [ ] Verificar que la aplicación carga correctamente
- [ ] Probar creación de reporte
- [ ] Verificar que se envían emails (revisar logs en `storage/logs/`)
- [ ] Verificar que el mapa muestra reportes con coordenadas
- [ ] Verificar login de administrador
- [ ] Revisar que el dashboard carga rápido (cache funcionando)
- [ ] Verificar assets minificados (inspeccionar en navegador)

## Comandos Útiles de Mantenimiento

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Limpiar logs antiguos
truncate -s 0 storage/logs/laravel.log

# Ver tamaño de cache
php artisan cache:table

# Estadísticas de queue
php artisan queue:monitor database --max=100
```

## Troubleshooting

### Los emails no se envían

1. Verificar que queue worker está corriendo: `supervisorctl status`
2. Revisar logs: `tail -f storage/logs/laravel.log`
3. Verificar credenciales SMTP en `.env`
4. Reintentar jobs fallidos: `php artisan queue:retry all`

### Dashboard carga lento

1. Verificar que cache está habilitado: `php artisan config:show cache.default`
2. Limpiar y regenerar cache: `php artisan cache:clear && php artisan config:cache`

### Assets no se cargan

1. Verificar que ejecutaste `npm run build`
2. Verificar permisos de `public/build/`
3. Limpiar cache del navegador
