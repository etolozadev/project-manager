#!/bin/sh
set -e

echo "==> Cacheando configuración, rutas y vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "$FRESH_MIGRATE" = "true" ]; then
    echo "==> Ejecutando migrate:fresh --seed..."
    php artisan migrate:fresh --seed --force
    echo "==> Listo. Elimina FRESH_MIGRATE del dashboard de Render."
else
    echo "==> Ejecutando migraciones..."
    php artisan migrate --force

    if [ "$RUN_SEEDERS" = "true" ]; then
        echo "==> Ejecutando seeders..."
        php artisan db:seed --force
        echo "==> Seeders completados. Puedes eliminar RUN_SEEDERS del dashboard de Render."
    fi
fi

echo "==> Servidor iniciado en puerto ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
