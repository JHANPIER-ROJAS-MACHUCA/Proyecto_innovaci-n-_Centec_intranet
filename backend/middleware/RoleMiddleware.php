<?php
require_once __DIR__ . '/AuthMiddleware.php';

class RoleMiddleware
{
    public static function require(array $allowed): array
    {
        $u = AuthMiddleware::requireAuth();
        if (!in_array((int)$u['tipoU'], $allowed, true)) {
            Response::error('Sin permiso para este módulo.', 403);
        }
        return $u;
    }
}
