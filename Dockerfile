# Imagen base con PHP 8.2 y Apache
FROM php:8.2-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_sqlite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar el proyecto Laravel
COPY . /var/www/html

# Configurar Apache para usar public/ de Laravel
RUN sed -i 's#/var/www/html#/var/www/html/public#' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's#/var/www/html#/var/www/html/public#' /etc/apache2/apache2.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite

# Definir el directorio de trabajo
WORKDIR /var/www/html

# Dar permisos a Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Instalar dependencias de Laravel (sin dev)
RUN composer install --no-dev --optimize-autoloader
RUN ls -l /opt/render/project/src/database/
RUN echo "📂 Directorios en /opt/render/project/src:" && ls -l /opt/render/project/src
RUN echo "📂 Directorios en /opt/render/project/src/database:" && ls -l /opt/render/project/src/database || echo "⚠️ database no existe"
RUN echo "📂 Directorios en /opt/render/project/src/storage:" && ls -l /opt/render/project/src/storage || echo "⚠️ storage no existe"

# Exponer el puerto correcto
EXPOSE 80

# Ejecutar Apache en primer plano
CMD ["apache2-foreground"]
