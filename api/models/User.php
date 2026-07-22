<?php

namespace Api\Models;

use PDO;
use PDOException;

/**
 * Modelo de Usuario
 *
 * Se encarga de la comunicación con la tabla 'users' en la base de datos.
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
            // Consulta SQL para insertar
            $query = "INSERT INTO " . $this->table_name . "
                      SET
                        name = :name,
                        email = :email,
                        password = :password";

            // Preparar la declaración (statement)
            $stmt = $this->conn->prepare($query);

            // Sanitizar valores (eliminar etiquetas y espacios)
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->email = htmlspecialchars(strip_tags($this->email));
            
            // Hash de la contraseña (se asume que AuthController la pasa en texto plano o ya hasheada, 
            // pero es mejor práctica hashearla justo antes de guardarla,
            // sin embargo los requerimientos indican usar password_hash, lo haremos aquí)
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

            // Vincular (Bind) los valores
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password", $hashedPassword);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                return true;
            }
            return false;

        } catch (PDOException $e) {
            // Podrías manejar el logueo del error aquí
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
            // Consulta para buscar usuario por email
            $query = "SELECT id, name, email, password, created_at
                      FROM " . $this->table_name . "
                      WHERE email = ?
                      LIMIT 0,1";

            // Preparar la declaración
            $stmt = $this->conn->prepare($query);

            // Sanitizar
            $email = htmlspecialchars(strip_tags($email));

            // Vincular valor (el ? se reemplaza por el parámetro 1)
            $stmt->bindParam(1, $email);

            // Ejecutar
            $stmt->execute();

            // Verificar si el usuario existe
            $num = $stmt->rowCount();

            if ($num > 0) {
                // Obtener fila (row)
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Asignar los valores a las propiedades del objeto
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                $this->password = $row['password']; // Esta es la contraseña encriptada (hash)
                $this->created_at = $row['created_at'];

                return true;
            }

            return false;
            
        } catch (PDOException $e) {
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
