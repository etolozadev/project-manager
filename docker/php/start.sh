#!/bin/bash
set -e

echo "[startup] Optimizando Laravel..."

# Esperar a que la base de datos esté lista
until php artisan db:monitor --max=5 2>/dev/null; do
    echo "[startup] Esperando base de datos..."
    sleep 2
done 2>/dev/null || true

# Cachear todo lo posible
php artisan config:cache  && echo "[startup] config:cache OK"
php artisan route:cache   && echo "[startup] route:cache OK"
php artisan view:cache    && echo "[startup] view:cache OK"
php artisan event:cache   && echo "[startup] event:cache OK"

echo "[startup] Listo. Iniciando PHP-FPM..."
exec php-fpm
