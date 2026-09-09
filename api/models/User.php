<?php

namespace Api\Models;

use PDO;
use PDOException;

/**
 * Modelo de Usuario
 *
 * Se encarga de la comunicación con la tabla 'users' en la base de datos.
 * Incluye validación de roles contra whitelist y método de listado para admin.
 */
class User {
    // Conexión a la base de datos y nombre de la tabla
    private PDO $conn;
    private string $table_name = "users";

    // Propiedades del objeto
    public ?int $id = null;
    public string $name;
    public string $email;
    public string $password;
    public string $role = 'mesero';
    public ?string $created_at = null;

    /**
     * Constructor con la inyección de la conexión PDO
     *
     * @param PDO $db
     */
    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Registra un nuevo usuario en la base de datos
     *
     * @return bool True si el registro fue exitoso, False en caso contrario
     */
    public function register(): bool {
        try {
            // Validar que el rol sea válido (whitelist) antes de guardar
            if (!Role::isValid($this->role)) {
                $this->role = Role::DEFAULT_ROLE;
            }

            // Consulta SQL para insertar (sintaxis estándar compatible con MySQL y SQLite)
            $query = "INSERT INTO " . $this->table_name . "
                      (name, email, password, role)
                      VALUES (:name, :email, :password, :role)";

            // Preparar la declaración (statement)
            $stmt = $this->conn->prepare($query);

            // Sanitizar valores (eliminar etiquetas y espacios)
            $this->name  = htmlspecialchars(strip_tags($this->name));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->role  = htmlspecialchars(strip_tags($this->role));

            // Hash de la contraseña justo antes de guardar
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

            // Vincular (Bind) los valores
            $stmt->bindParam(":name",     $this->name);
            $stmt->bindParam(":email",    $this->email);
            $stmt->bindParam(":password", $hashedPassword);
            $stmt->bindParam(":role",     $this->role);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $this->id = (int)$this->conn->lastInsertId();
                return true;
            }
            return false;

        } catch (PDOException $e) {
            error_log("Error registering user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca un usuario por su correo electrónico
     *
     * @param string $email Correo electrónico a buscar
     * @return bool True si el usuario existe, False si no
     */
    public function findByEmail(string $email): bool {
        try {
            // Consulta para buscar usuario por email (LIMIT 1 compatible con MySQL y SQLite)
            $query = "SELECT id, name, email, password, role, created_at
                      FROM " . $this->table_name . "
                      WHERE email = ?
                      LIMIT 1";

            // Preparar la declaración
            $stmt = $this->conn->prepare($query);

            // Sanitizar
            $email = htmlspecialchars(strip_tags($email));

            // Vincular valor (el ? se reemplaza por el parámetro 1)
            $stmt->bindParam(1, $email);

            // Ejecutar
            $stmt->execute();

            // Obtener la fila directamente (rowCount() no funciona en SELECT con SQLite)
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row !== false) {
                // Asignar los valores a las propiedades del objeto
                $this->id         = $row['id'];
                $this->name       = $row['name'];
                $this->email      = $row['email'];
                $this->password   = $row['password']; // Contraseña encriptada (hash)
                $this->role       = $row['role'] ?? Role::DEFAULT_ROLE;
                $this->created_at = $row['created_at'];

                return true;
            }

            return false;

        } catch (PDOException $e) {
            error_log("Error finding user by email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca un usuario por su ID
     *
     * @param  int        $id  ID del usuario
     * @return array|null      Datos del usuario (sin password) o null si no existe
     */
    public function findById(int $id): ?array {
        try {
            $stmt = $this->conn->prepare(
                "SELECT id, name, email, role, created_at
                 FROM {$this->table_name}
                 WHERE id = ?
                 LIMIT 1"
            );
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row !== false ? $row : null;
        } catch (PDOException $e) {
            error_log("Error finding user by id: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene la lista completa de usuarios (sin contraseñas).
     * Solo debe llamarse desde endpoints protegidos con rol admin.
     *
     * @return array Lista de usuarios [{id, name, email, role, created_at}]
     */
    public function getAll(): array {
        try {
            $stmt = $this->conn->prepare(
                "SELECT id, name, email, role, created_at
                 FROM {$this->table_name}
                 ORDER BY created_at DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all users: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Actualiza el rol de un usuario (solo para admin).
     *
     * @param  int    $userId  ID del usuario a modificar
     * @param  string $newRole Nuevo rol (debe ser válido en whitelist)
     * @return bool
     */
    public function updateRole(int $userId, string $newRole): bool {
        // Validar el nuevo rol antes de actualizar
        if (!Role::isValid($newRole)) {
            return false;
        }

        try {
            $stmt = $this->conn->prepare(
                "UPDATE {$this->table_name} SET role = ? WHERE id = ?"
            );
            return $stmt->execute([$newRole, $userId]);
        } catch (PDOException $e) {
            error_log("Error updating user role: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica las credenciales del usuario para iniciar sesión
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login(string $email, string $password): bool {
        // Buscar al usuario por correo
        $userExists = $this->findByEmail($email);

        if ($userExists) {
            // Verificar si la contraseña en texto plano coincide con el hash en la DB
            if (password_verify($password, $this->password)) {
                return true;
            }
        }

        return false;
    }
}
