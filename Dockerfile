# =========================
# FRONTEND BUILD (Vue)
# =========================
FROM node:20-alpine AS frontend-builder

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build


# =========================
# BACKEND (Laravel + Apache)
# =========================
FROM php:8.2-apache

WORKDIR /var/www/html

# Dependencias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip unzip git curl ca-certificates \
    && update-ca-certificates

# PHP extensiones
RUN docker-php-ext-install pdo pdo_pgsql pgsql gd zip

# Apache rewrite
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Código
COPY . .

# Frontend build
COPY --from=frontend-builder /app/public/build ./public/build

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache

# Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

CMD ["docker-entrypoint.sh"]