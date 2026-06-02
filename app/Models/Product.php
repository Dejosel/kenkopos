<?php

namespace App\Models;

use PDO;

/**
 * Clase Product
 * Modelo encargado de interactuar con la tabla 'products' en la base de datos.
 */
class Product
{
    private PDO $conn;
    private string $table_name = "products";

    public ?int $product_id = null;
    public string $name;
    public string $sku;
    public float $price;
    public ?string $created_at = null;

    /**
     * Constructor de la clase
     * 
     * @param PDO $db Conexión a la base de datos
     */
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Obtiene todos los productos de la base de datos
     * 
     * @return array
     */
    public function readAll(): array
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Obtiene un producto específico por su ID
     * 
     * @param int $id ID del producto
     * @return array|false Retorna el arreglo de datos del producto o false si no existe
     */
    public function readById(int $id): array|false
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Crea un nuevo producto
     * 
     * @return bool True si es exitoso, False de lo contrario
     */
    public function create(): bool
    {
        $query = "INSERT INTO " . $this->table_name . " (name, sku, price) VALUES (:name, :sku, :price)";
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->sku = htmlspecialchars(strip_tags($this->sku));

        // Enlazar parámetros
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':sku', $this->sku);
        $stmt->bindParam(':price', $this->price);

        return $stmt->execute();
    }

    /**
     * Actualiza un producto existente
     * 
     * @return bool True si es exitoso, False de lo contrario
     */
    public function update(): bool
    {
        $query = "UPDATE " . $this->table_name . " SET name = :name, sku = :sku, price = :price WHERE product_id = :id";
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->sku = htmlspecialchars(strip_tags($this->sku));

        // Enlazar parámetros
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':sku', $this->sku);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':id', $this->product_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Elimina un producto de la base de datos
     * 
     * @param int $id ID del producto a eliminar
     * @return bool True si es exitoso, False de lo contrario
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
