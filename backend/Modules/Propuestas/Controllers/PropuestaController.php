<?php
// Módulo Propuestas — controlador (extraído de controllers/CatalogoController.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/../Services/PropuestaService.php';

class PropuestaController
{
    public static function list(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => PropuestaService::listar(), 'success' => true]);
    }

    public static function detalle(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        $p = PropuestaService::detalle((int) ($req->query['id'] ?? 0));
        if (!$p) Response::error('Propuesta no existe.', 404);
        Response::json(['data' => $p, 'success' => true]);
    }

    public static function crear(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        Response::json(['data' => PropuestaService::crear($req->body, (int) $user['idU']), 'success' => true], 201);
    }

    public static function evaluar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        try {
            PropuestaService::evaluar((int) ($req->body['id'] ?? 0), $req->body);
            Response::json(['success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public static function eliminar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        PropuestaService::eliminar((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }

    public static function responder(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        try {
            Response::json(['data' => PropuestaService::responder($req->body), 'success' => true], 201);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }
}
