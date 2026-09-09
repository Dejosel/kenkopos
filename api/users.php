<?php

/**
 * Punto de entrada para gestión de usuarios (Endpoint: /api/users.php)
 *
 * Restricciones de acceso por rol:
 *   GET → Requiere rol: admin (lista todos los usuarios)
 *   PUT → Requiere rol: admin (cambia el rol de un usuario)
 *
 * Nota: Solo admin puede acceder a este endpoint.
 */

// Incluir archivos necesarios
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/JWT.php';
require_once __DIR__ . '/helpers/Auth.php';
require_once __DIR__ . '/models/Role.php';
require_once __DIR__ . '/models/User.php';

use Api\Config\Database;
use Api\Models\User;
use Api\Models\Role;
use Api\Helpers\Response;
use Api\Helpers\Auth;

// Configurar encabezados CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, PUT, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar petición OPTIONS (Preflight de CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ── Todos los métodos requieren rol admin ─────────────────────────────────────
$authUser = Auth::requireRole(['admin']);

// Inicializar conexión y modelo
$database  = new Database();
$db        = $database->getConnection();
$userModel = new User($db);

// Obtener el método HTTP y el ID si viene por URL
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        // ── Listar todos los usuarios ─────────────────────────────────────────
        if ($id) {
            // Obtener un usuario específico
            $user = $userModel->findById($id);
            if ($user) {
                Response::success(200, "Usuario encontrado.", ["user" => $user]);
            } else {
                Response::error(404, "Usuario no encontrado.");
            }
        } else {
            // Listar todos
            $users = $userModel->getAll();
            Response::success(200, "Usuarios obtenidos correctamente.", [
                "users" => $users,
                "total" => count($users)
            ]);
        }
        break;

    case 'PUT':
        // ── Cambiar el rol de un usuario ──────────────────────────────────────
        $data = json_decode(file_get_contents("php://input"));

        // Obtener ID desde URL o body
        if (!$id && isset($data->id)) {
            $id = (int)$data->id;
        }

        if (!$id) {
            Response::error(400, "Se requiere el ID del usuario a modificar.");
            break;
        }

        if (empty($data->role)) {
            Response::error(400, "Se requiere el nuevo rol. Valores válidos: " . implode(', ', Role::ALL));
            break;
        }

        $newRole = trim($data->role);

        // Validar que el rol sea válido
        if (!Role::isValid($newRole)) {
            Response::error(400, "Rol inválido '{$newRole}'. Los roles permitidos son: " . implode(', ', Role::ALL));
            break;
        }

        // Verificar que el usuario existe
        $targetUser = $userModel->findById($id);
        if (!$targetUser) {
            Response::error(404, "Usuario con ID {$id} no encontrado.");
            break;
        }

        // Prevenir que un admin se cambie su propio rol (seguridad)
        if ((int)$authUser['sub'] === $id) {
            Response::error(400, "No puedes cambiar tu propio rol.");
            break;
        }

        if ($userModel->updateRole($id, $newRole)) {
            Response::success(200, "Rol del usuario actualizado correctamente.", [
                "user" => [
                    "id"       => $id,
                    "name"     => $targetUser['name'],
                    "email"    => $targetUser['email'],
                    "old_role" => $targetUser['role'],
                    "new_role" => $newRole
                ]
            ]);
        } else {
            Response::error(500, "No se pudo actualizar el rol del usuario.");
        }
        break;

    default:
        Response::error(405, "Método no permitido. Use GET o PUT.");
        break;
}
