FROM php:8.2-cli

# Instalar dependencias del sistema y de PHP necesarias para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /app

# Copiar el proyecto
COPY . .

# Instalar dependencias de PHP
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# Exponer el puerto del servidor interno
EXPOSE 8000

# Lanzar el servidor de desarrollo de Laravel en todas las interfaces de red
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
