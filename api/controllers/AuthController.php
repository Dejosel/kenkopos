<?php

namespace Api\Controllers;

use Api\Models\User;
use Api\Helpers\Response;

/**
 * Controlador de Autenticación
 *
 * Maneja toda la lógica del registro y autenticación de usuarios.
 */
class AuthController {
    private User $userModel;

    /**
     * Constructor con inyección del modelo User
     *
     * @param User $userModel
     */
    public function __construct(User $userModel) {
        $this->userModel = $userModel;
    }

    /**
     * Procesa la solicitud de registro de usuario
     *
     * @param object|array $data Datos recibidos en formato JSON
     * @return void
     */
    public function register($data): void {
        // Validar que se enviaron los campos obligatorios
        if (empty($data->name) || empty($data->email) || empty($data->password)) {
            Response::error(400, "Faltan campos obligatorios");
            return;
        }

        // Validar formato de email
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            Response::error(400, "Formato de correo electrónico inválido");
            return;
        }

        // Validar longitud de la contraseña (mínimo 6 caracteres)
        if (strlen(trim($data->password)) < 6) {
            Response::error(400, "La contraseña debe tener mínimo 6 caracteres");
            return;
        }

        // Verificar si el usuario ya existe con ese email
        if ($this->userModel->findByEmail($data->email)) {
            Response::error(409, "El usuario ya se encuentra registrado con este correo");
            return;
        }

        // Asignar los valores al modelo
        $this->userModel->name = trim($data->name);
        $this->userModel->email = trim($data->email);
        $this->userModel->password = trim($data->password); // El modelo se encarga de aplicar el hash

        // Intentar registrar al usuario
        if ($this->userModel->register()) {
            Response::success(201, "Usuario registrado correctamente");
        } else {
            // Error interno (ej: fallo en la consulta SQL)
            Response::error(500, "No se pudo registrar al usuario. Intente nuevamente.");
        }
    }

    /**
     * Procesa la solicitud de inicio de sesión
     *
     * @param object|array $data Datos recibidos en formato JSON
     * @return void
     */
    public function login($data): void {
        // Validar que se enviaron los campos obligatorios
        if (empty($data->email) || empty($data->password)) {
            Response::error(400, "Faltan campos obligatorios");
            return;
        }

        $email = trim($data->email);
        $password = trim($data->password);

        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error(400, "Formato de correo electrónico inválido");
            return;
        }

        // Intentar iniciar sesión con el modelo
        $isLoggedIn = $this->userModel->login($email, $password);

        if ($isLoggedIn) {
            // Preparar los datos del usuario para devolver
            // (Nunca devolver la contraseña, ni siquiera hasheada)
            $userData = [
                "user" => [
                    "id" => $this->userModel->id,
                    "name" => $this->userModel->name
                ]
            ];

            Response::success(200, "Autenticación satisfactoria", $userData);
        } else {
            // Contraseña incorrecta o usuario no encontrado
            Response::error(401, "Error en la autenticación");
        }
    }
}
