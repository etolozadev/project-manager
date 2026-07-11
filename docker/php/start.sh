#!/bin/bash
set -e

echo "[startup] Iniciando Project Manager V2..."

# ─── vendor/ en volumen Docker nativo ─────────────────────────────────
# Si el volumen está vacío (primer arranque o volumen recreado),
# instalar dependencias de Composer dentro del contenedor.
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "[startup] vendor/ vacío — ejecutando composer install..."
    cd /var/www && composer install --no-interaction --optimize-autoloader 2>&1 | tail -3
    echo "[startup] Dependencias instaladas."
fi

# ─── node_modules/ en volumen Docker nativo ───────────────────────────
if [ ! -d "/var/www/node_modules/.bin" ]; then
    echo "[startup] node_modules vacío — ejecutando npm install..."
    cd /var/www && npm install --silent 2>&1 | tail -3
    echo "[startup] npm instalado."
fi

# ─── Laravel cache ────────────────────────────────────────────────────
echo "[startup] Cacheando config/rutas/vistas..."
php artisan config:cache  2>&1 | grep -v "^$"
php artisan route:cache   2>&1 | grep -v "^$"
php artisan view:cache    2>&1 | grep -v "^$"
php artisan event:cache   2>&1 | grep -v "^$"

echo "[startup] ¡Listo! Iniciando PHP-FPM (5 workers estáticos)..."
exec php-fpm
