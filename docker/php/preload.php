<?php

/**
 * OPcache Preloading — carga el framework en memoria al iniciar PHP-FPM.
 * Usa el classmap de Composer para precargar en el orden correcto.
 */

$autoloader = '/var/www/vendor/autoload.php';

if (!file_exists($autoloader)) {
    return;
}

require_once $autoloader;

// Usar el classmap optimizado de Composer (ya resuelve dependencias en orden)
$classmapFile = '/var/www/vendor/composer/autoload_classmap.php';

if (!file_exists($classmapFile)) {
    return;
}

$classMap = require $classmapFile;

$count = 0;
foreach ($classMap as $class => $file) {
    // Solo precargar clases del framework y dependencias core
    // Excluir tests, stubs y archivos que causan conflictos
    if (
        str_contains($file, '/tests/') ||
        str_contains($file, '/test/') ||
        str_contains($file, 'Test.php') ||
        str_contains($file, '.stub')
    ) {
        continue;
    }

    // Solo clases de los paquetes más usados
    if (
        str_contains($file, '/illuminate/') ||
        str_contains($file, '/symfony/') ||
        str_contains($file, '/laravel/framework/') ||
        str_contains($file, '/psr/')
    ) {
        try {
            if (file_exists($file)) {
                opcache_compile_file($file);
                $count++;
            }
        } catch (\Throwable $e) {
            // Ignorar errores individuales — preload es best-effort
        }
    }
}
