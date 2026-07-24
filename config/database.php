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
    private string $host = 'sql213.infinityfree.com';
    private string $db_name = 'if0_42272128_kenkopos';
    private string $username = 'if0_42272128';
    private string $password = '3lv7dCMYsj';
    private ?PDO $conn = null;

    /**
     * Obtiene la conexión a la base de datos
     * 
     * @return PDO
     */
    /**
     * Obtiene la conexión a la base de datos.
     * Si falla la conexión a MySQL (remoto), hace fallback automático a SQLite local.
     * Además, inicializa el esquema y añade las semillas de productos de restaurante.
     * 
     * @return PDO
     */
    public function getConnection(): PDO
    {
        $this->conn = null;
        $driver = 'mysql';

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            // Establecer un timeout bajo (3 segundos) para no ralentizar la ejecución local en caso de que falle la red
            $options = [
                PDO::ATTR_TIMEOUT => 3,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            $driver = 'mysql';
        } catch (PDOException $exception) {
            // Intentar conectar a una base de datos SQLite local
            try {
                $sqliteDir = dirname(__DIR__) . '/database';
                if (!file_exists($sqliteDir)) {
                    mkdir($sqliteDir, 0777, true);
                }
                $sqlitePath = $sqliteDir . '/kenkopos.sqlite';
                $this->conn = new PDO("sqlite:" . $sqlitePath);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $driver = 'sqlite';
            } catch (PDOException $sqliteEx) {
                die("Error de conexión a base de datos MySQL y fallback SQLite: " . $sqliteEx->getMessage());
            }
        }

        // Inicializar esquema si es necesario (auto-migración)
        if ($this->conn) {
            $this->initializeDatabase($this->conn, $driver);
        }

        return $this->conn;
    }

    /**
     * Inicializa las tablas, columnas y datos semilla necesarios
     */
    private function initializeDatabase(PDO $conn, string $driver): void
    {
        try {
            if ($driver === 'sqlite') {
                $conn->exec("CREATE TABLE IF NOT EXISTS products (
                    product_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(255) NOT NULL,
                    sku VARCHAR(50) NOT NULL,
                    price DECIMAL(10,2) NOT NULL,
                    category VARCHAR(100) DEFAULT 'General',
                    color VARCHAR(50) DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");

                // Tabla users para el módulo de autenticación (evidencia AA5-EV01)
                $conn->exec("CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
            } else {
                $conn->exec("CREATE TABLE IF NOT EXISTS products (
                    product_id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    sku VARCHAR(50) NOT NULL,
                    price DECIMAL(10,2) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");

                // Tabla users para el módulo de autenticación (evidencia AA5-EV01)
                $conn->exec("CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY email (email)
                )");
            }

            // Asegurarnos de que existan las columnas category y color
            $this->ensureColumnsExist($conn, $driver);

            // Sembrar datos de restaurante si la tabla está vacía
            $this->seedRestaurantData($conn);
        } catch (PDOException $e) {
            // Silenciosamente continuar o registrar error para evitar que bloquee la ejecución general
            error_log("Error inicializando base de datos KenkoPOS: " . $e->getMessage());
        }
    }

    /**
     * Asegura que las columnas category y color existan en la tabla products
     */
    private function ensureColumnsExist(PDO $conn, string $driver): void
    {
        $existingColumns = [];
        if ($driver === 'sqlite') {
            $stmt = $conn->query("PRAGMA table_info(products)");
            $columns = $stmt->fetchAll();
            foreach ($columns as $col) {
                $existingColumns[] = strtolower($col['name']);
            }
        } else {
            $stmt = $conn->query("DESCRIBE products");
            $columns = $stmt->fetchAll();
            foreach ($columns as $col) {
                $existingColumns[] = strtolower($col['Field']);
            }
        }

        if (!in_array('category', $existingColumns)) {
            $conn->exec("ALTER TABLE products ADD COLUMN category VARCHAR(100) DEFAULT 'General'");
        }
        if (!in_array('color', $existingColumns)) {
            $conn->exec("ALTER TABLE products ADD COLUMN color VARCHAR(50) DEFAULT NULL");
        }
    }

    /**
     * Inserta productos semilla de restaurante si no hay ninguno registrado
     */
    private function seedRestaurantData(PDO $conn): void
    {
        $stmt = $conn->query("SELECT COUNT(*) FROM products");
        $totalCount = $stmt->fetchColumn();

        // Si la tabla está vacía o tiene menos de 4 productos, sembramos comida para simular un POS
        if ($totalCount < 4) {
            $seeds = [
                ['name' => 'Hamburguesa Especial', 'sku' => 'PLT-001', 'price' => 22000.00, 'category' => 'Platos Fuertes', 'color' => '#dc3545'],
                ['name' => 'Pizza Margarita', 'sku' => 'PLT-002', 'price' => 18000.00, 'category' => 'Platos Fuertes', 'color' => '#dc3545'],
                ['name' => 'Perro Caliente Gigante', 'sku' => 'PLT-003', 'price' => 14000.00, 'category' => 'Platos Fuertes', 'color' => '#dc3545'],
                ['name' => 'Costillas BBQ', 'sku' => 'PLT-004', 'price' => 28000.00, 'category' => 'Platos Fuertes', 'color' => '#dc3545'],
                ['name' => 'Papas Fritas', 'sku' => 'ENT-001', 'price' => 6500.00, 'category' => 'Entradas', 'color' => '#ffc107'],
                ['name' => 'Empanadas de la Casa (3 und)', 'sku' => 'ENT-002', 'price' => 8000.00, 'category' => 'Entradas', 'color' => '#ffc107'],
                ['name' => 'Aros de Cebolla', 'sku' => 'ENT-003', 'price' => 7000.00, 'category' => 'Entradas', 'color' => '#ffc107'],
                ['name' => 'Limonada Natural', 'sku' => 'BEB-001', 'price' => 5000.00, 'category' => 'Bebidas', 'color' => '#198754'],
                ['name' => 'Gaseosa Coca-Cola', 'sku' => 'BEB-002', 'price' => 4000.00, 'category' => 'Bebidas', 'color' => '#198754'],
                ['name' => 'Cerveza Club Colombia', 'sku' => 'BEB-003', 'price' => 7000.00, 'category' => 'Bebidas', 'color' => '#198754'],
                ['name' => 'Torta de Tres Leches', 'sku' => 'POS-001', 'price' => 9000.00, 'category' => 'Postres', 'color' => '#6f42c1'],
                ['name' => 'Volcán de Chocolate', 'sku' => 'POS-002', 'price' => 11000.00, 'category' => 'Postres', 'color' => '#6f42c1']
            ];

            $insertStmt = $conn->prepare("INSERT INTO products (name, sku, price, category, color) VALUES (:name, :sku, :price, :category, :color)");
            foreach ($seeds as $product) {
                $insertStmt->execute($product);
            }
        }
    }
}
