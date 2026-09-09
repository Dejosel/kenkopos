<?php

namespace Api\Helpers;

/**
 * Clase JWT (JSON Web Token)
 *
 * Implementación nativa de JWT con algoritmo HS256 (HMAC-SHA256).
 * No requiere ninguna dependencia externa ni Composer.
 *
 * Estructura del token: base64url(header).base64url(payload).base64url(signature)
 */
class JWT {

    /**
     * Codifica un payload y genera un token JWT firmado con HS256
     *
     * @param array  $payload  Datos a incluir en el token (ej: user_id, role)
     * @param string $secret   Clave secreta para firmar el token
     * @param int    $expTime  Tiempo de expiración en segundos (default: 8 horas)
     * @return string          Token JWT generado
     */
    public static function encode(array $payload, string $secret, int $expTime = 28800): string {
        // 1. Header — algoritmo HS256 y tipo JWT
        $header = self::base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]));

        // 2. Payload — agregar iat (issued at) y exp (expiration) automáticamente
        $payload['iat'] = time();
        $payload['exp'] = time() + $expTime;

        $payload = self::base64UrlEncode(json_encode($payload));

        // 3. Firma — HMAC-SHA256 sobre header.payload
        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );

        return "$header.$payload.$signature";
    }

    /**
     * Decodifica y valida un token JWT
     *
     * @param string $token   Token JWT a validar
     * @param string $secret  Clave secreta usada al firmar
     * @return array          Payload decodificado si el token es válido
     * @throws \Exception     Si el token es inválido, malformado o expirado
     */
    public static function decode(string $token, string $secret): array {
        // Separar las tres partes del token
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new \Exception('Token JWT malformado: se esperan 3 segmentos.');
        }

        [$header, $payload, $signature] = $parts;

        // Verificar la firma recalculándola y comparando
        $expectedSignature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );

        // Comparación segura contra ataques de tiempo (timing attacks)
        if (!hash_equals($expectedSignature, $signature)) {
            throw new \Exception('Firma JWT inválida. El token ha sido alterado.');
        }

        // Decodificar el payload
        $decodedPayload = json_decode(self::base64UrlDecode($payload), true);

        if (!is_array($decodedPayload)) {
            throw new \Exception('Payload JWT inválido o corrupto.');
        }

        // Verificar la expiración del token
        if (isset($decodedPayload['exp']) && $decodedPayload['exp'] < time()) {
            throw new \Exception('Token JWT expirado. Por favor inicie sesión nuevamente.');
        }

        return $decodedPayload;
    }

    /**
     * Extrae el token Bearer del encabezado Authorization
     *
     * @return string|null  Token sin el prefijo "Bearer ", o null si no existe
     */
    public static function extractFromHeader(): ?string {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        // Soporte para servidores Apache que no pasan Authorization automáticamente
        if (empty($authHeader) && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization'] ?? '';
        }

        // También intentar con getallheaders() para mayor compatibilidad
        if (empty($authHeader) && function_exists('getallheaders')) {
            $allHeaders = getallheaders();
            $authHeader = $allHeaders['Authorization'] ?? $allHeaders['authorization'] ?? '';
        }

        if (stripos($authHeader, 'Bearer ') === 0) {
            return substr($authHeader, 7);
        }

        return null;
    }

    // ─── Métodos privados de utilidad ───────────────────────────────────────

    /**
     * Codifica en Base64 URL-safe (sin padding =)
     */
    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodifica Base64 URL-safe (restaura padding si es necesario)
     */
    private static function base64UrlDecode(string $data): string {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
