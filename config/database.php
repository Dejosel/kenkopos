<?php

namespace Config;

use PDO;
use PDOException;

/**
 * Clase Database para gestionar la conexión a MySQL usando PDO.
 * Aplica el patrón Singleton para mantener una única instancia de conexión.
 */
class Database
{
    private string $host = 'localhost';
    private string $db_name = 'kenkopos_db';
    private string $username = 'root';
    private string $password = '';
    private ?PDO $conn = null;

    /**
     * Obtiene la conexión a la base de datos
     * 
     * @return PDO
     */
    public function getConnection(): PDO
    {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            // Configurar PDO para que lance excepciones en caso de error
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            die("Error de conexión a la base de datos: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
