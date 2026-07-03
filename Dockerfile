# --- Etapa 1: Compilar Vue.js con Node ---
FROM node:20-alpine AS frontend-builder
WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build


# --- Etapa 2: Servidor PHP con Apache ---
FROM php:8.2-apache

WORKDIR /var/www/html

# Instalar dependencias del sistema + extensiones PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    zip \
    unzip \
    git \
    curl \
    ca-certificates \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd \
    && update-ca-certificates

# Habilitar rewrite de Apache
RUN a2enmod rewrite

# Cambiar DocumentRoot a Laravel /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copiar proyecto completo
COPY . .

# Copiar build de Vue desde etapa 1
COPY --from=frontend-builder /app/public/build ./public/build

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-interaction --optimize-autoloader --no-dev

# Permisos Laravel
RUN chown -R www-data:www-data /var/www/html/storage \
 && chown -R www-data:www-data /var/www/html/bootstrap/cache

# Optimización Laravel (BUILD TIME, NO runtime)
RUN php artisan optimize:clear \
 && php artisan config:cache \
 && php artisan route:cache

EXPOSE 80

# 🚀 SOLO ARRANCA APACHE (NO MIGRATIONS AQUÍ)
CMD apache2-foreground