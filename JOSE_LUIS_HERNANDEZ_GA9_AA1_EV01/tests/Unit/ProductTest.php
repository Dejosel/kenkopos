<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Product;
use PDO;

/**
 * Pruebas Unitarias para el Modelo Product de KenkoPOS
 */
class ProductTest extends TestCase
{
    private PDO $db;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        // Crear base de datos en memoria para pruebas unitarias aisladas
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->exec("CREATE TABLE products (
            product_id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            sku VARCHAR(50) NOT NULL UNIQUE,
            price DECIMAL(10,2) NOT NULL,
            category VARCHAR(100) DEFAULT 'General',
            color VARCHAR(50) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $this->product = new Product($this->db);
    }

    /**
     * Prueba los valores predeterminados del modelo
     */
    public function testDefaultProductProperties(): void
    {
        $this->assertNull($this->product->product_id);
        $this->assertEquals('General', $this->product->category);
        $this->assertNull($this->product->color);
    }

    /**
     * Prueba la creación e inserción de un producto en base de datos
     */
    public function testCreateProductSuccessful(): void
    {
        $this->product->name = 'Hamburguesa Clásica Test';
        $this->product->sku = 'TST-001';
        $this->product->price = 18000.00;
        $this->product->category = 'Platos Fuertes';
        $this->product->color = '#dc3545';

        $result = $this->product->create();

        $this->assertTrue($result, 'El método create() debe retornar true al insertar un producto válido.');

        // Verificar lectura
        $allProducts = $this->product->readAll();
        $this->assertCount(1, $allProducts, 'Debe haber exactamente 1 producto en la base de datos.');
        $this->assertEquals('Hamburguesa Clásica Test', $allProducts[0]['name']);
        $this->assertEquals('TST-001', $allProducts[0]['sku']);
        $this->assertEquals(18000.00, (float)$allProducts[0]['price']);
    }

    /**
     * Prueba la lectura de un producto por su ID
     */
    public function testReadProductById(): void
    {
        $this->product->name = 'Pizza Margarita Test';
        $this->product->sku = 'TST-002';
        $this->product->price = 22000.00;
        $this->product->category = 'Pizzas';
        $this->product->color = '#ffc107';
        $this->product->create();

        $found = $this->product->readById(1);

        $this->assertIsArray($found, 'El método readById() debe retornar un array si el producto existe.');
        $this->assertEquals('Pizza Margarita Test', $found['name']);
        $this->assertEquals('TST-002', $found['sku']);
    }

    /**
     * Prueba la actualización de datos de un producto
     */
    public function testUpdateProduct(): void
    {
        $this->product->name = 'Jugo Natural';
        $this->product->sku = 'TST-003';
        $this->product->price = 5000.00;
        $this->product->category = 'Bebidas';
        $this->product->create();

        // Actualizar precio y nombre
        $this->product->product_id = 1;
        $this->product->name = 'Jugo Natural Grande';
        $this->product->price = 6500.00;
        $this->product->category = 'Bebidas';
        $this->product->color = '#198754';

        $updated = $this->product->update();
        $this->assertTrue($updated, 'El método update() debe retornar true tras una actualización exitosa.');

        $found = $this->product->readById(1);
        $this->assertEquals('Jugo Natural Grande', $found['name']);
        $this->assertEquals(6500.00, (float)$found['price']);
    }

    /**
     * Prueba la eliminación de un producto por ID
     */
    public function testDeleteProduct(): void
    {
        $this->product->name = 'Postre Temporal';
        $this->product->sku = 'TST-004';
        $this->product->price = 8000.00;
        $this->product->category = 'Postres';
        $this->product->create();

        $deleted = $this->product->delete(1);
        $this->assertTrue($deleted, 'El método delete() debe retornar true tras eliminar el producto.');

        $found = $this->product->readById(1);
        $this->assertFalse($found, 'El producto eliminado no debe ser encontrado en base de datos.');
    }
}
