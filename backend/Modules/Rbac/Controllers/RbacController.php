<?php
// Módulo Rbac — controlador (extraído de services/RbacService.php, phase2-modular).
require_once __DIR__ . '/../Services/RbacService.php';

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
