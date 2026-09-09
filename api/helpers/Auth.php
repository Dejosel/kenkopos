<?php

namespace Api\Helpers;

/**
 * Clase Auth (Middleware de Autenticación y Autorización)
 *
 * Centraliza la lógica de validación de tokens JWT y control de acceso por rol.
 * Se usa al inicio de cada endpoint protegido.
 *
 * Uso rápido:
 *   // Solo verifica que el token sea válido (cualquier rol):
 *   $authUser = Auth::requireAuth();
 *
 *   // Verifica token Y que el rol esté permitido:
 *   $authUser = Auth::requireRole(['admin', 'cajero']);
 */
class Auth {

    /**
     * Roles válidos del sistema.
     * Sirve como whitelist para prevenir roles arbitrarios.
     */
    public const ROLES = ['admin', 'cajero', 'mesero'];

    /**
     * Permisos por endpoint (referencia documental).
     * La restricción real se aplica llamando a requireRole() en cada endpoint.
     */
    public const PERMISSIONS = [
        'admin'  => ['products.write', 'products.read', 'orders.read', 'orders.write', 'users.manage'],
        'cajero' => ['products.read', 'orders.read', 'orders.write'],
        'mesero' => ['products.read'],
    ];

    /**
     * Valida que la petición incluya un token JWT válido.
     * Si el token no existe o es inválido, responde con HTTP 401 y termina.
     *
     * @return array  Payload del token con los datos del usuario autenticado
     */
    public static function requireAuth(): array {
        // Asegurarse de que las constantes de configuración estén cargadas
        if (!defined('JWT_SECRET')) {
            require_once __DIR__ . '/../config/config.php';
        }

        require_once __DIR__ . '/JWT.php';

        $token = JWT::extractFromHeader();

        if (!$token) {
            Response::error(401, 'Acceso no autorizado. Se requiere token de autenticación (Bearer).');
        }

        try {
            $payload = JWT::decode($token, JWT_SECRET);
        } catch (\Exception $e) {
            Response::error(401, 'Token inválido o expirado: ' . $e->getMessage());
        }

        return $payload;
    }

    /**
     * Valida el token JWT Y verifica que el rol del usuario esté en la lista permitida.
     * Si el token no existe/es inválido → HTTP 401.
     * Si el rol no está permitido → HTTP 403.
     *
     * @param  array $allowedRoles  Lista de roles con permiso (ej: ['admin', 'cajero'])
     * @return array                Payload del token con datos del usuario autenticado
     */
    public static function requireRole(array $allowedRoles): array {
        // Primero validar la autenticación
        $payload = self::requireAuth();

        // Luego validar el rol
        $userRole = $payload['role'] ?? '';

        if (!in_array($userRole, $allowedRoles, true)) {
            Response::error(
                403,
                "Acceso denegado. Tu rol '{$userRole}' no tiene permiso para esta operación. " .
                "Roles permitidos: " . implode(', ', $allowedRoles)
            );
        }

        return $payload;
    }

    /**
     * Intenta obtener el usuario del token sin forzar autenticación.
     * Útil para endpoints que son opcionales (públicos, pero enriquecen la respuesta si hay token).
     *
     * @return array|null  Payload del usuario o null si no hay token válido
     */
    public static function getAuthUser(): ?array {
        if (!defined('JWT_SECRET')) {
            require_once __DIR__ . '/../config/config.php';
        }

        require_once __DIR__ . '/JWT.php';

        $token = JWT::extractFromHeader();
        if (!$token) {
            return null;
        }

        try {
            return JWT::decode($token, JWT_SECRET);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Verifica si un rol dado es válido dentro del sistema.
     *
     * @param  string $role  Rol a verificar
     * @return bool
     */
    public static function isValidRole(string $role): bool {
        return in_array($role, self::ROLES, true);
    }
}
