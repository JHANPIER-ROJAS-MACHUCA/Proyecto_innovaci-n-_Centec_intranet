<?php
// Módulo Catalogos — controlador de adjuntos (extraído de
// controllers/OperacionController.php, phase2-modular; idéntico).
require_once __DIR__ . '/../Services/AdjuntoService.php';

class AdjuntoController
{
    public static function listar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => AdjuntoService::listar((int) ($req->query['customer_id'] ?? 0)), 'success' => true]);
    }

    public static function crear(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => AdjuntoService::crear($req->body), 'success' => true], 201);
    }

    public static function eliminar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        AdjuntoService::eliminar((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }

    public static function subir(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        if (empty($_FILES['file'])) Response::error('Archivo requerido.', 422);
        try {
            $name = AdjuntoService::guardarArchivo($_FILES['file']);
            Response::json(['data' => ['name' => $name], 'success' => true], 201);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
