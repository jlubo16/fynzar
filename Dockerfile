# Imagen oficial PHP
FROM php:8.2-cli

# Carpeta de trabajo
WORKDIR /app

# Instalar dependencias del sistema y Node.js (necesario para Vite)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    curl && \
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_pgsql zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar el código del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias de JS y compilar activos (Vite)
# Esto generará el manifest.json que te falta
RUN npm install
RUN npm run build

# Ajustar permisos para evitar errores 500 de escritura
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

# Puerto que usa Render
EXPOSE 10000

# Comando de inicio: 
# 1. Limpiamos caché vieja
# 2. Migramos (sin borrar todo cada vez)
# 3. Encendemos el servidor
CMD php artisan config:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000