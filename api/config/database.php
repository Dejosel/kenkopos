<?php

namespace Api\Config;

use PDO;
use PDOException;

// Requerir el archivo de configuración si las constantes no están definidas
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/config.php';
}

/**
 * Clase Database
 *
 * Maneja la conexión a la base de datos MySQL utilizando PDO.
 * Implementa el patrón Singleton o simplemente retorna una instancia.
 */
class Database {
    private ?PDO $conn = null;

    /**
     * Obtiene la conexión a la base de datos
     *
     * @return PDO|null Retorna la instancia PDO o null si falla
     */
    public function getConnection(): ?PDO {
        $this->conn = null;

        try {
            // DSN de conexión
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            // Opciones de PDO para un manejo seguro y eficiente
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            // Crear instancia de PDO
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $exception) {
            // Manejar excepción de conexión
            // En un entorno de producción, esto debería registrarse en un archivo de log
            // y no mostrarse directamente para evitar exponer datos sensibles
            echo json_encode([
                "success" => false, 
                "message" => "Error de conexión a la base de datos: " . $exception->getMessage()
            ]);
            exit;
        }

        return $this->conn;
    }
}
