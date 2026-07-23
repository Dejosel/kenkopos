<?php

/**
 * Punto de entrada RESTful para Productos (Endpoint: /api/products.php)
 * Este archivo adapta la lógica existente (MVC) para que el Frontend React 
 * pueda consumirla mediante peticiones JSON.
 */

// Incluir archivos necesarios del backend existente y de la API
require_once __DIR__ . '/../app/Models/Product.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/Response.php';

use Api\Config\Database;
use App\Models\Product;
use Api\Helpers\Response;

// Configurar encabezados CORS y JSON adicionales si es necesario
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar petición OPTIONS (Preflight de CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Inicializar conexión y modelo
$database = new Database();
$db = $database->getConnection();
$productModel = new Product($db);

// Obtener el método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obtener ID si viene por URL (ej: /api/products.php?id=5)
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id) {
            // Obtener un producto específico
            $product = $productModel->readById($id);
            if ($product) {
                Response::success(200, "Producto encontrado", ["product" => $product]);
            } else {
                Response::error(404, "Producto no encontrado");
            }
        } else {
            // Obtener todos los productos
            $products = $productModel->readAll();
            // Siempre devolver éxito, incluso si está vacío
            Response::success(200, "Productos obtenidos correctamente", ["products" => $products]);
        }
        break;

    case 'POST':
        // Crear un nuevo producto
        $data = json_decode(file_get_contents("php://input"));

        // Validaciones básicas
        if (empty($data->name) || empty($data->sku) || empty($data->price) || empty($data->category)) {
            Response::error(400, "Datos incompletos. Nombre, SKU, precio y categoría son obligatorios.");
            break;
        }

        if ($data->price <= 0) {
            Response::error(400, "El precio debe ser mayor a 0.");
            break;
        }

        // Asignar datos al modelo
        $productModel->name = trim($data->name);
        $productModel->sku = trim($data->sku);
        $productModel->price = (float)$data->price;
        $productModel->category = trim($data->category);
        $productModel->color = !empty($data->color) ? trim($data->color) : null;

        if ($productModel->create()) {
            Response::success(201, "Producto creado correctamente");
        } else {
            Response::error(500, "No se pudo crear el producto");
        }
        break;

    case 'PUT':
        // Actualizar un producto existente
        $data = json_decode(file_get_contents("php://input"));
        
        // Si no viene ID por GET, intentar leer del body
        if (!$id && isset($data->product_id)) {
            $id = (int)$data->product_id;
        }

        if (!$id) {
            Response::error(400, "Se requiere el ID del producto a actualizar.");
            break;
        }

        if (empty($data->name) || empty($data->sku) || empty($data->price) || empty($data->category)) {
            Response::error(400, "Datos incompletos para actualizar.");
            break;
        }

        // Verificar que el producto existe
        if (!$productModel->readById($id)) {
            Response::error(404, "Producto no encontrado para actualizar.");
            break;
        }

        // Asignar datos al modelo
        $productModel->product_id = $id;
        $productModel->name = trim($data->name);
        $productModel->sku = trim($data->sku);
        $productModel->price = (float)$data->price;
        $productModel->category = trim($data->category);
        $productModel->color = !empty($data->color) ? trim($data->color) : null;

        if ($productModel->update()) {
            Response::success(200, "Producto actualizado correctamente");
        } else {
            Response::error(500, "No se pudo actualizar el producto");
        }
        break;

    case 'DELETE':
        // Eliminar producto
        if (!$id) {
            Response::error(400, "Se requiere el ID del producto a eliminar.");
            break;
        }

        // Verificar existencia
        if (!$productModel->readById($id)) {
            Response::error(404, "Producto no encontrado para eliminar.");
            break;
        }

        if ($productModel->delete($id)) {
            Response::success(200, "Producto eliminado correctamente");
        } else {
            Response::error(500, "No se pudo eliminar el producto");
        }
        break;

    default:
        Response::error(405, "Método no permitido");
        break;
}
