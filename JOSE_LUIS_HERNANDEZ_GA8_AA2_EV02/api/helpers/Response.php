<?php

namespace Api\Helpers;

/**
 * Clase Response
 *
 * Se encarga de centralizar todas las respuestas JSON de la API.
 * Mantiene la consistencia en el formato de respuesta.
 */
class Response {
    /**
     * Envía una respuesta JSON exitosa
     *
     * @param int $statusCode Código HTTP (ej: 200, 201)
     * @param string $message Mensaje descriptivo
     * @param array $data Datos adicionales (opcional)
     * @return void
     */
    public static function success(int $statusCode, string $message, array $data = []): void {
        self::send($statusCode, true, $message, $data);
    }

    /**
     * Envía una respuesta JSON de error
     *
     * @param int $statusCode Código HTTP (ej: 400, 401, 409)
     * @param string $message Mensaje descriptivo del error
     * @return void
     */
    public static function error(int $statusCode, string $message): void {
        self::send($statusCode, false, $message);
    }

    /**
     * Método interno para formatear y enviar la respuesta final
     *
     * @param int $statusCode
     * @param bool $success
     * @param string $message
     * @param array $data
     * @return void
     */
    private static function send(int $statusCode, bool $success, string $message, array $data = []): void {
        // Establecer encabezados para JSON
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        http_response_code($statusCode);

        $response = [
            'success' => $success,
            'message' => $message
        ];

        // Solo incluir data si no está vacía o es un éxito (ej. datos del usuario en el login)
        if (!empty($data) || ($success && isset($data['user']))) {
            // Combinar la respuesta base con la data proporcionada
            $response = array_merge($response, $data);
        }

        echo json_encode($response);
        exit;
    }
}
