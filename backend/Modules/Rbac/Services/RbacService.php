<?php
// Módulo Rbac — servicio (movido de services/, phase2-modular; idéntico).
require_once __DIR__ . '/../Repositories/RbacRepository.php';

class RbacService
{
    private static array $cache = [];

    // "modulo.vista.accion" (o "modulo.vista" = cualquier acción de la vista)
    public static function tiene(int $idRol, string $permiso): bool
    {
        if (!isset(self::$cache[$idRol])) {
            self::$cache[$idRol] = RbacRepository::mapaRol($idRol);
        }
        $map = self::$cache[$idRol];
        if (isset($map[$permiso])) return true;
        if (substr_count($permiso, '.') === 1) {
            foreach ($map as $k => $_) {
                if (strpos($k, $permiso . '.') === 0) return true;
            }
        }
        return false;
    }

    public static function misPermisos(int $idRol): array
    {
        if (!isset(self::$cache[$idRol])) {
            self::$cache[$idRol] = RbacRepository::mapaRol($idRol);
        }
        return self::$cache[$idRol];
    }

    public static function matriz(): array
    {
        return [
            'roles' => RbacRepository::roles(),
            'permisos' => RbacRepository::permisos(),
            'asignaciones' => RbacRepository::asignaciones(),
        ];
    }
}

class PermissionMiddleware
{
    // Bloquea también el acceso directo por URL a vistas no autorizadas.
    public static function require(string $permiso): array
    {
        $u = AuthMiddleware::requireAuth();
        if (!RbacService::tiene((int) $u['tipoU'], $permiso)) {
            Response::error('Sin permiso para este módulo.', 403);
        }
        return $u;
    }

    // OR lógico: pasa si tiene al menos uno.
    public static function requireAny(array $permisos): array
    {
        $u = AuthMiddleware::requireAuth();
        foreach ($permisos as $p) {
            if (RbacService::tiene((int) $u['tipoU'], $p)) return $u;
        }
        Response::error('Sin permiso para este módulo.', 403);
    }
}
