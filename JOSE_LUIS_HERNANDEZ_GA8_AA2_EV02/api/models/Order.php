<?php

namespace Api\Models;

use PDO;
use PDOException;

/**
 * Modelo de Orden / Venta
 *
 * Se encarga de la comunicación con las tablas 'orders' y 'order_items'.
 */
class Order {
    private PDO $conn;
    private string $table_name = "orders";
    private string $items_table_name = "order_items";

    // Propiedades de la orden
    public ?int $order_id = null;
    public string $table_name_val;
    public float $subtotal;
    public int $discount_percent = 0;
    public float $discount_amount = 0.00;
    public int $tax_percent = 0;
    public float $tax_amount = 0.00;
    public float $total;
    public string $payment_method;
    public float $cash_received = 0.00;
    public float $change_amount = 0.00;
    public string $operator_name = 'Admin';
    public string $operator_role = 'admin';
    public ?string $created_at = null;

    // Ítems asociados a la orden
    public array $items = [];

    /**
     * Constructor con la inyección de la conexión PDO
     *
     * @param PDO $db
     */
    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Guarda la orden y sus detalles en una transacción PDO
     *
     * @return bool True si se guardó todo correctamente, False en caso contrario
     */
    public function save(): bool {
        try {
            // Iniciar transacción para asegurar atomicidad
            $this->conn->beginTransaction();

            $query = "INSERT INTO " . $this->table_name . "
                      (table_name, subtotal, discount_percent, discount_amount, tax_percent, tax_amount, total, payment_method, cash_received, change_amount, operator_name, operator_role)
                      VALUES (:table_name, :subtotal, :discount_percent, :discount_amount, :tax_percent, :tax_amount, :total, :payment_method, :cash_received, :change_amount, :operator_name, :operator_role)";

            $stmt = $this->conn->prepare($query);

            // Sanitizar valores de texto
            $this->table_name_val = htmlspecialchars(strip_tags($this->table_name_val));
            $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
            $this->operator_name = htmlspecialchars(strip_tags($this->operator_name));
            $this->operator_role = htmlspecialchars(strip_tags($this->operator_role));

            // Vincular parámetros
            $stmt->bindParam(":table_name", $this->table_name_val);
            $stmt->bindParam(":subtotal", $this->subtotal);
            $stmt->bindParam(":discount_percent", $this->discount_percent);
            $stmt->bindParam(":discount_amount", $this->discount_amount);
            $stmt->bindParam(":tax_percent", $this->tax_percent);
            $stmt->bindParam(":tax_amount", $this->tax_amount);
            $stmt->bindParam(":total", $this->total);
            $stmt->bindParam(":payment_method", $this->payment_method);
            $stmt->bindParam(":cash_received", $this->cash_received);
            $stmt->bindParam(":change_amount", $this->change_amount);
            $stmt->bindParam(":operator_name", $this->operator_name);
            $stmt->bindParam(":operator_role", $this->operator_role);

            if (!$stmt->execute()) {
                $this->conn->rollBack();
                return false;
            }

            // Obtener el ID autogenerado de la orden
            $this->order_id = (int)$this->conn->lastInsertId();

            // Insertar los ítems del ticket
            $queryItems = "INSERT INTO " . $this->items_table_name . "
                           (order_id, product_id, name, sku, price, qty)
                           VALUES (:order_id, :product_id, :name, :sku, :price, :qty)";

            $stmtItems = $this->conn->prepare($queryItems);

            foreach ($this->items as $item) {
                $product_id = (int)$item['product_id'];
                $name = htmlspecialchars(strip_tags($item['name']));
                $sku = htmlspecialchars(strip_tags($item['sku']));
                $price = (float)$item['price'];
                $qty = (int)$item['qty'];

                $stmtItems->bindParam(":order_id", $this->order_id);
                $stmtItems->bindParam(":product_id", $product_id);
                $stmtItems->bindParam(":name", $name);
                $stmtItems->bindParam(":sku", $sku);
                $stmtItems->bindParam(":price", $price);
                $stmtItems->bindParam(":qty", $qty);

                if (!$stmtItems->execute()) {
                    $this->conn->rollBack();
                    return false;
                }
            }

            // Confirmar transacción
            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error saving order: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el historial de todas las órdenes guardadas junto con sus detalles
     *
     * @return array Lista de órdenes
     */
    public function readAll(): array {
        try {
            $query = "SELECT order_id, table_name, subtotal, discount_percent, discount_amount, tax_percent, tax_amount, total, payment_method, cash_received, change_amount, operator_name, operator_role, created_at
                      FROM " . $this->table_name . "
                      ORDER BY created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Cargar los productos correspondientes para cada orden
            foreach ($orders as $key => $order) {
                $queryItems = "SELECT item_id, product_id, name, sku, price, qty
                               FROM " . $this->items_table_name . "
                               WHERE order_id = ?";
                $stmtItems = $this->conn->prepare($queryItems);
                $stmtItems->execute([$order['order_id']]);
                $orders[$key]['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            }

            return $orders;

        } catch (PDOException $e) {
            error_log("Error reading orders: " . $e->getMessage());
            return [];
        }
    }
}
