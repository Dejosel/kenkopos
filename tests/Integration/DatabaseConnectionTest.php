<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Config\Database;
use PDO;

/**
 * Pruebas de Integración para la Conexión y Esquema de Base de Datos de KenkoPOS
 */
class DatabaseConnectionTest extends TestCase
{
    private PDO $conn;

    protected function setUp(): void
    {
        parent::setUp();
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Valida que la conexión retorne una instancia válida de PDO
     */
    public function testDatabaseConnectionReturnsPDOInstance(): void
    {
        $this->assertInstanceOf(PDO::class, $this->conn, 'La conexión obtenida debe ser una instancia de PDO.');
    }

    /**
     * Valida que el modo de error esté configurado para lanzar excepciones
     */
    public function testDatabaseErrorModeIsException(): void
    {
        $errMode = $this->conn->getAttribute(PDO::ATTR_ERRMODE);
        $this->assertEquals(PDO::ERRMODE_EXCEPTION, $errMode, 'El atributo ATTR_ERRMODE debe ser ERRMODE_EXCEPTION.');
    }

    /**
     * Valida que existan las tablas maestras requeridas para el sistema POS
     */
    public function testMasterTablesExist(): void
    {
        $driver = $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $stmt = $this->conn->query("SELECT name FROM sqlite_master WHERE type='table'");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $stmt = $this->conn->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        $tables = array_map('strtolower', $tables);

        $this->assertContains('products', $tables, 'La tabla products debe existir en la base de datos.');
        $this->assertContains('users', $tables, 'La tabla users debe existir en la base de datos.');
        $this->assertContains('orders', $tables, 'La tabla orders debe existir en la base de datos.');
        $this->assertContains('order_items', $tables, 'La tabla order_items debe existir en la base de datos.');
    }

    /**
     * Valida el comportamiento de transacciones atómicas (ACID) y rollback
     */
    public function testTransactionRollback(): void
    {
        $this->conn->beginTransaction();
        $this->assertTrue($this->conn->inTransaction(), 'La base de datos debe reportar que está en una transacción.');

        // Insertar un registro temporal
        $stmt = $this->conn->prepare("INSERT INTO products (name, sku, price, category) VALUES ('Temp Rollback Product', 'TMP-999', 1000.00, 'Test')");
        $stmt->execute();

        // Revertir transacción
        $this->conn->rollBack();
        $this->assertFalse($this->conn->inTransaction(), 'La transacción debe haber finalizado tras el rollback.');

        // Comprobar que no quedó persistido
        $checkStmt = $this->conn->prepare("SELECT COUNT(*) FROM products WHERE sku = 'TMP-999'");
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        $this->assertEquals(0, (int)$count, 'El registro insertado antes del rollback no debe persistir en la base de datos.');
    }
}
