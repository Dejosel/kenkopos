<?php

/**
 * Punto de entrada RESTful para órdenes/comandas (Endpoint: /api/orders.php)
 *
 * Restricciones de acceso por rol:
 *   GET  → Requiere token válido (admin o cajero)
 *   POST → Requiere rol: admin o cajero
 */

// Incluir archivos necesarios
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/models/Order.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/JWT.php';
require_once __DIR__ . '/helpers/Auth.php';

use Api\Config\Database;
use Api\Models\Order;
use Api\Helpers\Response;
use Api\Helpers\Auth;

// Configurar encabezados CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar petición OPTIONS (Preflight de CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Inicializar conexión y modelo
$database   = new Database();
$db         = $database->getConnection();
$orderModel = new Order($db);

// Obtener el método HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // ── PROTEGIDO — admin y cajero pueden ver el historial ───────────────
        $authUser = Auth::requireRole(['admin', 'cajero']);

        $orders = $orderModel->readAll();
        Response::success(200, "Historial de órdenes obtenido correctamente.", ["orders" => $orders]);
        break;

    case 'POST':
        // ── PROTEGIDO — admin y cajero pueden crear órdenes ──────────────────
        $authUser = Auth::requireRole(['admin', 'cajero']);

        $data = json_decode(file_get_contents("php://input"));

        // Validaciones básicas de campos obligatorios
        if (
            empty($data->table_name) ||
            !isset($data->subtotal) ||
            !isset($data->total) ||
            empty($data->payment_method) ||
            empty($data->items) ||
            !is_array($data->items)
        ) {
            Response::error(400, "Datos incompletos para guardar la comanda.");
            break;
        }

        // Usar datos del operador desde el token JWT (más seguro que confiar en el body)
        $orderModel->table_name_val  = trim($data->table_name);
        $orderModel->subtotal        = (float)$data->subtotal;
        $orderModel->discount_percent = isset($data->discount_percent) ? (int)$data->discount_percent : 0;
        $orderModel->discount_amount  = isset($data->discount_amount)  ? (float)$data->discount_amount : 0.00;
        $orderModel->tax_percent      = isset($data->tax_percent)      ? (int)$data->tax_percent       : 0;
        $orderModel->tax_amount       = isset($data->tax_amount)       ? (float)$data->tax_amount      : 0.00;
        $orderModel->total            = (float)$data->total;
        $orderModel->payment_method   = trim($data->payment_method);
        $orderModel->cash_received    = isset($data->cash_received)    ? (float)$data->cash_received   : 0.00;
        $orderModel->change_amount    = isset($data->change_amount)    ? (float)$data->change_amount   : 0.00;

        // Operador tomado del JWT (no del body — evita suplantación de identidad)
        $orderModel->operator_name = $authUser['name'] ?? 'Sistema';
        $orderModel->operator_role = $authUser['role'] ?? 'cajero';

        // Mapear items
        $orderModel->items = [];
        foreach ($data->items as $item) {
            if (empty($item->product_id) || empty($item->name) || empty($item->price) || empty($item->qty)) {
                Response::error(400, "Faltan detalles en los productos de la orden.");
                exit;
            }
            $orderModel->items[] = [
                'product_id' => $item->product_id,
                'name'       => $item->name,
                'sku'        => !empty($item->sku) ? $item->sku : '',
                'price'      => $item->price,
                'qty'        => $item->qty
            ];
        }

        // Intentar guardar en base de datos
        if ($orderModel->save()) {
            Response::success(201, "Comanda guardada correctamente en la base de datos.", [
                "order_id" => $orderModel->order_id
            ]);
        } else {
            Response::error(500, "No se pudo registrar la comanda en la base de datos.");
        }
        break;

    default:
        Response::error(405, "Método no permitido.");
        break;
}
