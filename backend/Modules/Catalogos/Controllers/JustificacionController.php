<?php
// Módulo Catalogos — controlador de justificaciones (extraído de
// controllers/OperacionController.php, phase2-modular; idéntico).
require_once __DIR__ . '/../Services/JustificacionService.php';

class JustificacionController
{
    public static function listar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        $cid = isset($req->query['credit_id']) && $req->query['credit_id'] !== '' ? (int) $req->query['credit_id'] : null;
        Response::json(['data' => JustificacionService::listar($cid), 'success' => true]);
    }

    public static function crear(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => JustificacionService::crear($req->body), 'success' => true], 201);
    }

    public static function actualizar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        JustificacionService::actualizar((int) ($req->body['id'] ?? 0), $req->body);
        Response::json(['success' => true]);
    }

    public static function eliminar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        JustificacionService::eliminar((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }
}
