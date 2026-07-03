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

# Instalar extensiones necesarias de PHP y certificados CA indispensables para SSL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    zip \
    unzip \
    git \
    ca-certificates \
    && update-ca-certificates \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd

# Habilitar el módulo rewrite de Apache
RUN a2enmod rewrite

# Cambiar la raíz de Apache a la carpeta /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copiar el código del proyecto
COPY . .

# Copiar los archivos compilados de Vue desde la Etapa 1
COPY --from=frontend-builder /app/public/build ./public/build

# Instalar Composer y las dependencias de PHP
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Dar permisos correctos a las carpetas de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# 🔽 REEMPLAZAMOS LA ÚLTIMA LÍNEA POR ESTA 🔽
CMD php artisan migrate:fresh --seed --force && apache2-foreground