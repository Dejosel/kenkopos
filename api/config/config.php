<?php

/**
 * Archivo de configuración principal
 *
 * Contiene las credenciales para la conexión a la base de datos
 * y otras configuraciones globales.
 */

// Configuración de Base de Datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Usuario por defecto de XAMPP/Laragon
define('DB_PASS', '');     // Contraseña por defecto (vacía en XAMPP)
define('DB_NAME', 'kenkopos');
define('DB_CHARSET', 'utf8mb4');

// Configuración JWT (JSON Web Tokens)
define('JWT_SECRET', 'KenkoPOS_S3cr3t_K3y_2026!@#$%^&*()_JWT_HMAC256');
define('JWT_EXPIRATION', 28800); // 8 horas en segundos (un turno laboral completo)
