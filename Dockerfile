# Imagen oficial PHP
FROM php:8.2-cli

# Carpeta de trabajo
WORKDIR /app

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip

# Instalar extensiones PHP necesarias para Laravel + PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar proyecto
COPY . .

# Instalar dependencias Laravel
RUN composer install --no-dev --optimize-autoloader

# Generar cache (opcional pero recomendado)
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Puerto que usa Render
EXPOSE 10000

# Comando de inicio
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000





























































