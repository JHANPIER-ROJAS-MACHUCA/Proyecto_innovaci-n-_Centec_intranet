<?php
// Módulo Catalogos — controlador de metas (extraído de
// controllers/OperacionController.php, phase2-modular; idéntico).
require_once __DIR__ . '/../Services/MetaService.php';

class MetaController
{
    public static function listar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => MetaService::listar(), 'success' => true]);
    }

    public static function crear(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        Response::json(['data' => MetaService::crear($req->body), 'success' => true], 201);
    }

    public static function actualizar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        MetaService::actualizar((int) ($req->body['id'] ?? 0), $req->body);
        Response::json(['success' => true]);
    }

    public static function eliminar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        MetaService::eliminar((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }

    public static function porUsuario(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => MetaService::porUsuario(), 'success' => true]);
    }
}
