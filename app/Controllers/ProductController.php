<?php

namespace App\Controllers;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Helpers/Response.php';

use Config\Database;
use App\Models\Product;
use App\Helpers\Response;

/**
 * Controlador para gestionar las peticiones relacionadas con los productos.
 */
class ProductController
{
    private Product $productModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->productModel = new Product($db);
    }

    /**
     * Obtiene el listado completo de productos
     */
    public function index(): array
    {
        return $this->productModel->readAll();
    }

    /**
     * Obtiene los datos de un producto específico
     */
    public function show(int $id): array|false
    {
        return $this->productModel->readById($id);
    }

    /**
     * Guarda un producto nuevo a partir de una petición POST
     */
    public function store(array $postData): void
    {
        if (!empty($postData['name']) && !empty($postData['sku']) && !empty($postData['price'])) {
            $this->productModel->name = $postData['name'];
            $this->productModel->sku = $postData['sku'];
            $this->productModel->price = (float)$postData['price'];

            if ($this->productModel->create()) {
                Response::redirect('list.php?msg=created');
            } else {
                Response::redirect('create.php?error=create_failed');
            }
        } else {
            Response::redirect('create.php?error=missing_data');
        }
    }

    /**
     * Actualiza un producto existente
     */
    public function update(array $postData): void
    {
        if (!empty($postData['product_id']) && !empty($postData['name']) && !empty($postData['sku']) && !empty($postData['price'])) {
            $this->productModel->product_id = (int)$postData['product_id'];
            $this->productModel->name = $postData['name'];
            $this->productModel->sku = $postData['sku'];
            $this->productModel->price = (float)$postData['price'];

            if ($this->productModel->update()) {
                Response::redirect('list.php?msg=updated');
            } else {
                Response::redirect("edit.php?id={$this->productModel->product_id}&error=update_failed");
            }
        } else {
            $id = $postData['product_id'] ?? '';
            Response::redirect("edit.php?id={$id}&error=missing_data");
        }
    }

    /**
     * Elimina un producto por su ID
     */
    public function destroy(int $id): void
    {
        if ($this->productModel->delete($id)) {
            Response::redirect('list.php?msg=deleted');
        } else {
            Response::redirect('list.php?error=delete_failed');
        }
    }
}
