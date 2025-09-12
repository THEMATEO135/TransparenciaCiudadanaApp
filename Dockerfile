# Imagen base con PHP 8 y extensiones necesarias
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_sqlite

# Copiar proyecto
COPY . /var/www/html

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configurar Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Cachear config de Laravel
RUN php artisan config:cache && php artisan route:cache

# Exponer puerto que usa Render
EXPOSE 10000
CMD ["apache2-foreground"]
