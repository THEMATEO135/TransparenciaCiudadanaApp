# Usa la imagen oficial de PHP 8.4 con Apache
FROM php:8.4-apache

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instala extensiones de PHP necesarias para Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    exif \
    pcntl \
    bcmath

# Habilita mod_rewrite de Apache y configura el DocumentRoot
RUN a2enmod rewrite && \
    sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's!AllowOverride None!AllowOverride All!g' /etc/apache2/sites-available/000-default.conf

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia solo los archivos necesarios para composer install primero
COPY composer.json composer.lock* ./

# Crea directorios necesarios para evitar errores de autoload
RUN mkdir -p database/seeders database/factories app/Models

# Instala las dependencias de Composer
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-progress

# Copia TODO el resto del código de la aplicación
COPY . .

# Ajusta los permisos de los directorios de Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expone el puerto 80
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]