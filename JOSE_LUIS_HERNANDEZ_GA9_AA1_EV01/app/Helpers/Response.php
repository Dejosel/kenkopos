<?php

namespace App\Helpers;

/**
 * Helper para facilitar las respuestas HTTP y redirecciones.
 */
class Response
{
    /**
     * Redirige a una URL específica
     * 
     * @param string $url Ruta de destino
     */
    public static function redirect(string $url): void
    {
        header("Location: " . $url);
        exit();
    }
}
