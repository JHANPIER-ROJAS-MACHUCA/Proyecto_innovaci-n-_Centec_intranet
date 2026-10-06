<?php

namespace CrediSoporte\Core;

// Respuestas JSON con formato unico: { success, data, message }.
class Response
{
    public static function json($data = null, string $message = 'OK', int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $status < 400,
            'data' => $data,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE);
    }

    public static function error(string $message = 'Error', int $status = 400, $data = null): void
    {
        static::json($data, $message, $status);
    }

    public static function notFound(string $message = 'Recurso no encontrado'): void
    {
        static::error($message, 404);
    }
}
