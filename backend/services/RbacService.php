<?php
require_once __DIR__ . '/../repositories/RbacRepository.php';

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

class RbacController
{
    public static function misPermisos(AppRequest $req): void
    {
        $u = AuthMiddleware::requireAuth();
        Response::json(['data' => RbacService::misPermisos((int) $u['tipoU']), 'rol' => (int) $u['tipoU'], 'success' => true]);
    }

    public static function matriz(AppRequest $req): void
    {
        RoleMiddleware::require([1]);
        Response::json(['data' => RbacService::matriz(), 'success' => true]);
    }

    public static function asignar(AppRequest $req): void
    {
        RoleMiddleware::require([1]);
        $idRol = (int) ($req->body['idRol'] ?? 0);
        if (!$idRol) Response::error('idRol requerido.', 422);
        RbacRepository::reemplazarAsignaciones($idRol, (array) ($req->body['permisos'] ?? []));
        Response::json(['success' => true]);
    }

    public static function permisoEstado(AppRequest $req): void
    {
        RoleMiddleware::require([1]);
        RbacRepository::setEstado((int) ($req->body['id'] ?? 0), (int) ($req->body['estado'] ?? 1));
        Response::json(['success' => true]);
    }

    public static function roles(AppRequest $req): void
    {
        RoleMiddleware::require([1]);
        Response::json(['data' => RbacRepository::roles(), 'success' => true]);
    }
}
