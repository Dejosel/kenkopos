<?php

namespace Api\Config;

use PDO;

/**
 * Clase Database (Adaptador)
 *
 * Delega la conexión a la clase Database principal del proyecto KenkoPOS,
 * que ya gestiona la conexión a InfinityFree con fallback automático a SQLite.
 * Esto evita duplicar configuraciones y garantiza coherencia entre módulos.
 */
class Database {

    /**
     * Obtiene la conexión PDO reutilizando la configuración central del proyecto.
     *
     * @return PDO Instancia de la conexión activa
     */
    public function getConnection(): PDO {
        // Incluir la clase Database principal si aún no ha sido cargada
        if (!class_exists('Config\Database')) {
            require_once __DIR__ . '/../../config/database.php';
        }

        // Instanciar y retornar la conexión del proyecto principal
        $mainDb = new \Config\Database();
        return $mainDb->getConnection();
    }
}
