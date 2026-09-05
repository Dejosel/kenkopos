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

            // Asegurarnos de que existan las columnas category y color en products
            $this->ensureColumnsExist($conn, $driver);

            // Asegurar columna role en users y crear tablas de comandas/ventas
            $this->ensureUserRoleColumnExists($conn, $driver);
            $this->initializeOrderTables($conn, $driver);

            // Sembrar datos de restaurante si la tabla está vacía
            $this->seedRestaurantData($conn);

            // Sembrar usuarios con roles por defecto
            $this->seedUserData($conn);
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
     * Asegura que exista la columna role en la tabla users
     */
    private function ensureUserRoleColumnExists(PDO $conn, string $driver): void
    {
        $existingColumns = [];
        if ($driver === 'sqlite') {
            $stmt = $conn->query("PRAGMA table_info(users)");
            $columns = $stmt->fetchAll();
            foreach ($columns as $col) {
                $existingColumns[] = strtolower($col['name']);
            }
        } else {
            $stmt = $conn->query("DESCRIBE users");
            $columns = $stmt->fetchAll();
            foreach ($columns as $col) {
                $existingColumns[] = strtolower($col['Field']);
            }
        }

        if (!in_array('role', $existingColumns)) {
            $conn->exec("ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'mesero'");
        }
    }

    /**
     * Inicializa las tablas orders y order_items para persistir las ventas
     */
    private function initializeOrderTables(PDO $conn, string $driver): void
    {
        if ($driver === 'sqlite') {
            $conn->exec("CREATE TABLE IF NOT EXISTS orders (
                order_id INTEGER PRIMARY KEY AUTOINCREMENT,
                table_name VARCHAR(100) NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL,
                discount_percent INTEGER DEFAULT 0,
                discount_amount DECIMAL(10,2) DEFAULT 0.00,
                tax_percent INTEGER DEFAULT 0,
                tax_amount DECIMAL(10,2) DEFAULT 0.00,
                total DECIMAL(10,2) NOT NULL,
                payment_method VARCHAR(50) NOT NULL,
                cash_received DECIMAL(10,2) DEFAULT 0.00,
                change_amount DECIMAL(10,2) DEFAULT 0.00,
                operator_name VARCHAR(100) DEFAULT 'Admin',
                operator_role VARCHAR(50) DEFAULT 'admin',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            $conn->exec("CREATE TABLE IF NOT EXISTS order_items (
                item_id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                name VARCHAR(255) NOT NULL,
                sku VARCHAR(50) NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                qty INTEGER NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(order_id) REFERENCES orders(order_id)
            )");
        } else {
            $conn->exec("CREATE TABLE IF NOT EXISTS orders (
                order_id INT AUTO_INCREMENT PRIMARY KEY,
                table_name VARCHAR(100) NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL,
                discount_percent INT DEFAULT 0,
                discount_amount DECIMAL(10,2) DEFAULT 0.00,
                tax_percent INT DEFAULT 0,
                tax_amount DECIMAL(10,2) DEFAULT 0.00,
                total DECIMAL(10,2) NOT NULL,
                payment_method VARCHAR(50) NOT NULL,
                cash_received DECIMAL(10,2) DEFAULT 0.00,
                change_amount DECIMAL(10,2) DEFAULT 0.00,
                operator_name VARCHAR(100) DEFAULT 'Admin',
                operator_role VARCHAR(50) DEFAULT 'admin',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            $conn->exec("CREATE TABLE IF NOT EXISTS order_items (
                item_id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                sku VARCHAR(50) NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                qty INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(order_id) REFERENCES orders(order_id) ON DELETE CASCADE
            )");
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

    /**
     * Sienbra usuarios con roles por defecto
     */
    private function seedUserData(PDO $conn): void
    {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");

        $seeds = [
            [
                'name' => 'Jose Admin',
                'email' => 'admin@kenkopos.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin'
            ],
            [
                'name' => 'Ana Cajera',
                'email' => 'cajero@kenkopos.com',
                'password' => password_hash('cajero123', PASSWORD_DEFAULT),
                'role' => 'cajero'
            ],
            [
                'name' => 'Carlos Mesero',
                'email' => 'mesero@kenkopos.com',
                'password' => password_hash('mesero123', PASSWORD_DEFAULT),
                'role' => 'mesero'
            ]
        ];

        $insertStmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        
        foreach ($seeds as $user) {
            $stmt->execute([':email' => $user['email']]);
            $exists = $stmt->fetchColumn();
            if ($exists == 0) {
                $insertStmt->execute($user);
            }
        }
    }
}
