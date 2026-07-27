<?php

/**
 * Punto de entrada para el registro de usuarios (Endpoint: POST /api/register.php)
 */

// Incluir archivos necesarios
require_once __DIR__ . '/../config/database.php'; // DB principal (InfinityFree + SQLite fallback)
require_once __DIR__ . '/config/database.php';     // Adaptador que delega al principal
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/controllers/AuthController.php';

use Api\Config\Database;
use Api\Models\User;
use Api\Controllers\AuthController;
use Api\Helpers\Response;

// Verificar que el método sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error(405, "Método no permitido. Utilice POST.");
}

// Obtener la conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Instanciar el modelo y controlador
$user = new User($db);
$authController = new AuthController($user);

// Obtener los datos JSON enviados en el cuerpo (body) de la petición
$data = json_decode(file_get_contents("php://input"));

// Enviar los datos al controlador para procesar el registro
$authController->register($data);
