#!/bin/sh
set -e

echo "==> Ejecutando migraciones..."
php artisan migrate --force

echo "==> Cacheando configuración, rutas y vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Servidor iniciado en puerto ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
