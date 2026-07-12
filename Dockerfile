# ── Stage 1: Compilar assets frontend ─────────────────────────────────────────
FROM node:22-alpine AS frontend

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ── Stage 2: Imagen PHP de producción ─────────────────────────────────────────
FROM php:8.3-cli-alpine

# Dependencias del sistema
RUN apk add --no-cache \
    git curl zip unzip \
    libpng-dev libzip-dev icu-dev oniguruma-dev \
    freetype-dev libjpeg-turbo-dev \
    postgresql-dev

# Extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Redis extension (necesita autoconf/g++/make solo para compilar, se eliminan después)
RUN apk add --no-cache --virtual .build-deps autoconf g++ make \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar código fuente
COPY . .

# Copiar assets compilados desde stage 1
COPY --from=frontend /app/public/build ./public/build

# Instalar dependencias PHP (solo producción)
RUN composer update fakerphp/faker --no-interaction --no-scripts \
    && composer install --no-dev --optimize-autoloader --no-interaction

# Crear directorios de storage necesarios y permisos
RUN mkdir -p storage/framework/views \
              storage/framework/cache/data \
              storage/framework/sessions \
    && chmod -R 775 storage bootstrap/cache

# Script de arranque
COPY docker/render-start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
