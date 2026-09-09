<?php

namespace Api\Controllers;

use Api\Models\User;
use Api\Models\Role;
use Api\Helpers\Response;
use Api\Helpers\JWT;
use Api\Helpers\Auth;

/**
 * Controlador de Autenticación
 *
 * Maneja toda la lógica del registro y autenticación de usuarios.
 * Al autenticar correctamente, genera y devuelve un JWT firmado con HS256.
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
     * Procesa la solicitud de registro de usuario.
     *
     * Reglas de asignación de rol:
     * - Si NO viene un token de admin → rol forzado a 'mesero' (registro público)
     * - Si viene token de admin válido → acepta el rol enviado en el body
     *
     * @param object|array $data Datos recibidos en formato JSON
     * @return void
     */
    public function register($data): void {
        // Validar que se enviaron los campos obligatorios
        if (empty($data->name) || empty($data->email) || empty($data->password)) {
            Response::error(400, "Faltan campos obligatorios: name, email, password.");
            return;
        }

        // Validar formato de email
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            Response::error(400, "Formato de correo electrónico inválido.");
            return;
        }

        // Validar longitud de la contraseña (mínimo 6 caracteres)
        if (strlen(trim($data->password)) < 6) {
            Response::error(400, "La contraseña debe tener mínimo 6 caracteres.");
            return;
        }

        // Verificar si el usuario ya existe con ese email
        if ($this->userModel->findByEmail($data->email)) {
            Response::error(409, "El usuario ya se encuentra registrado con este correo.");
            return;
        }

        // ── Lógica de asignación de rol ──────────────────────────────────────
        // Por defecto todo registro público asigna rol 'mesero'
        $assignedRole = Role::DEFAULT_ROLE;

        // Si viene un rol solicitado, solo se acepta si el solicitante es admin
        if (!empty($data->role) && $data->role !== Role::MESERO) {
            $authUser = Auth::getAuthUser(); // Intenta obtener usuario del token (no fuerza auth)

            if ($authUser && isset($authUser['role']) && $authUser['role'] === Role::ADMIN) {
                // El solicitante es admin → puede asignar cualquier rol válido
                if (Role::isValid($data->role)) {
                    $assignedRole = trim($data->role);
                } else {
                    Response::error(400, "Rol inválido. Los roles permitidos son: " . implode(', ', Role::ALL));
                    return;
                }
            } else {
                // No es admin → ignorar el rol solicitado y asignar mesero
                $assignedRole = Role::DEFAULT_ROLE;
            }
        }

        // Asignar los valores al modelo
        $this->userModel->name     = trim($data->name);
        $this->userModel->email    = trim($data->email);
        $this->userModel->password = trim($data->password); // El modelo aplica el hash
        $this->userModel->role     = $assignedRole;

        // Intentar registrar al usuario
        if ($this->userModel->register()) {
            Response::success(201, "Usuario registrado correctamente.", [
                "user" => [
                    "id"    => $this->userModel->id,
                    "name"  => $this->userModel->name,
                    "email" => $this->userModel->email,
                    "role"  => $this->userModel->role
                ]
            ]);
        } else {
            Response::error(500, "No se pudo registrar al usuario. Intente nuevamente.");
        }
    }

    /**
     * Procesa la solicitud de inicio de sesión.
     * Si las credenciales son correctas, genera y devuelve un JWT.
     *
     * @param object|array $data Datos recibidos en formato JSON
     * @return void
     */
    public function login($data): void {
        // Validar que se enviaron los campos obligatorios
        if (empty($data->email) || empty($data->password)) {
            Response::error(400, "Faltan campos obligatorios: email, password.");
            return;
        }

        $email    = trim($data->email);
        $password = trim($data->password);

        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error(400, "Formato de correo electrónico inválido.");
            return;
        }

        // Intentar iniciar sesión con el modelo
        $isLoggedIn = $this->userModel->login($email, $password);

        if ($isLoggedIn) {
            // ── Generar JWT con datos del usuario autenticado ─────────────────
            if (!defined('JWT_SECRET')) {
                require_once __DIR__ . '/../config/config.php';
            }

            $tokenPayload = [
                'sub'   => $this->userModel->id,    // Subject: ID del usuario
                'name'  => $this->userModel->name,
                'email' => $this->userModel->email,
                'role'  => $this->userModel->role,
            ];

            $token = JWT::encode($tokenPayload, JWT_SECRET, JWT_EXPIRATION);

            // Devolver token + datos del usuario (NUNCA la contraseña)
            Response::success(200, "Autenticación satisfactoria.", [
                "token" => $token,
                "token_type" => "Bearer",
                "expires_in" => JWT_EXPIRATION,
                "user" => [
                    "id"    => $this->userModel->id,
                    "name"  => $this->userModel->name,
                    "email" => $this->userModel->email,
                    "role"  => $this->userModel->role
                ]
            ]);
        } else {
            // Contraseña incorrecta o usuario no encontrado
            Response::error(401, "Credenciales incorrectas. Verifique email y contraseña.");
        }
    }
}
