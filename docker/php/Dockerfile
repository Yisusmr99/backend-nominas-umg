# Usar la imagen base de PHP con FPM
FROM php:8.3-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www

# Primero, copiar TODO el código del proyecto
COPY . /var/www/

# Asegurar permisos en storage y bootstrap/cache
RUN chmod -R 777 storage bootstrap/cache

# Ahora instalar dependencias de Laravel
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Generar la clave de la aplicación
RUN php artisan key:generate

# Comando de inicio
CMD ["php-fpm"]