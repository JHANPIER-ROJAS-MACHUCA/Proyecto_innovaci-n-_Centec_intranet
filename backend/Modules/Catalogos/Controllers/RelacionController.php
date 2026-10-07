<?php
// Módulo Catalogos — controlador de vinculaciones (extraído de
// controllers/CatalogoController.php, phase2-modular; idéntico).
require_once __DIR__ . '/../Services/RelacionService.php';

class RelacionController
{
    public static function list(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => RelacionService::listar((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
    }

    public static function add(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        try {
            Response::json(['data' => RelacionService::agregar($req->body), 'success' => true], 201);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), strpos($e->getMessage(), 'no existe') !== false ? 404 : 422);
        }
    }

    public static function delete(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        RelacionService::eliminar((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }
}
