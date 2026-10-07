<?php
class Response
{
    public static function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function ok($data = null): void
    {
        self::json(['ok' => true, 'data' => $data]);
    }

    public static function error(string $message, int $status = 400, $details = null): void
    {
        self::json(['ok' => false, 'message' => $message, 'details' => $details], $status);
    }

    public static function legacyBool(bool $ok): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo $ok ? '1' : '0';
        exit;
    }
}
