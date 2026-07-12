#!/bin/sh
set -e

echo "==> Cacheando configuración, rutas y vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Ejecutando migraciones..."
php artisan migrate --force

if [ "$RUN_SEEDERS" = "true" ]; then
    echo "==> Ejecutando seeders..."
    php artisan db:seed --force
    echo "==> Seeders completados. Puedes eliminar RUN_SEEDERS del dashboard de Render."
fi

echo "==> Servidor iniciado en puerto ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
