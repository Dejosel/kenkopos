<?php

namespace Api\Models;

use PDO;
use PDOException;

/**
 * Modelo de Rol
 *
 * Gestiona el catálogo de roles del sistema y su comunicación con la BD.
 * Expone constantes para evitar strings mágicos en el código.
 */
class Role {
    // Conexión a la base de datos
    private PDO $conn;
    private string $table_name = "roles";

    // ── Constantes de roles del sistema ──────────────────────────────────────
    public const ADMIN  = 'admin';
    public const CAJERO = 'cajero';
    public const MESERO = 'mesero';

    /** Lista completa de roles válidos — whitelist definitiva */
    public const ALL = [self::ADMIN, self::CAJERO, self::MESERO];

    /** Rol asignado por defecto al registrarse sin token de admin */
    public const DEFAULT_ROLE = self::MESERO;

    /**
     * @param PDO $db Conexión activa a la base de datos
     */
    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Obtiene todos los roles registrados en la tabla 'roles'.
     *
     * @return array Lista de roles [{id, name, description, created_at}]
     */
    public function getAll(): array {
        try {
            $stmt = $this->conn->prepare("SELECT id, name, description FROM {$this->table_name} ORDER BY id ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching roles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca un rol por su nombre.
     *
     * @param  string     $name  Nombre del rol (ej: 'admin')
     * @return array|null        Datos del rol o null si no existe
     */
    public function findByName(string $name): ?array {
        try {
            $stmt = $this->conn->prepare("SELECT id, name, description FROM {$this->table_name} WHERE name = ? LIMIT 1");
            $stmt->execute([$name]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row !== false ? $row : null;
        } catch (PDOException $e) {
            error_log("Error finding role: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verifica si un nombre de rol es válido en el sistema.
     * Usa la whitelist de constantes para evitar roles arbitrarios.
     *
     * @param  string $role  Nombre del rol a validar
     * @return bool
     */
    public static function isValid(string $role): bool {
        return in_array($role, self::ALL, true);
    }
}
