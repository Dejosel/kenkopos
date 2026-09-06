<?php
/**
 * Bootstrap file for PHPUnit tests in KenkoPOS
 */

$baseDir = dirname(__DIR__);

// Forzar carga de los modelos y clases nativas de KenkoPOS (PHP Web Stack)
require_once $baseDir . '/config/database.php';
require_once $baseDir . '/app/Models/Product.php';
require_once $baseDir . '/app/Controllers/ProductController.php';
require_once $baseDir . '/app/Helpers/Response.php';
require_once $baseDir . '/api/models/Order.php';
require_once $baseDir . '/api/models/User.php';

spl_autoload_register(function ($class) use ($baseDir) {
    $class = ltrim($class, '\\');

    if (str_starts_with($class, 'Config\\')) {
        $file = $baseDir . '/config/' . str_replace('\\', '/', substr($class, 7)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    if (str_starts_with($class, 'App\\')) {
        $file = $baseDir . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    if (str_starts_with($class, 'Api\\')) {
        $file = $baseDir . '/api/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
}, true, true);
