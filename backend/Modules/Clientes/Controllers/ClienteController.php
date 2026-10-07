<?php
// Módulo Clientes — controlador (movido de controllers/, phase2-modular; idéntico).
require_once __DIR__ . '/../Services/ClienteService.php';

class ClienteController
{
    public static function create(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        $errors = ClienteService::validate($req->body);
        if ($errors) Response::json(['errors' => $errors, 'success' => false], 422);
        Response::json(['data' => ClienteService::create($req->body), 'success' => true], 201);
    }

    public static function update(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        try {
            ClienteService::update((int) ($req->body['idCG'] ?? 0), $req->body);
            Response::json(['success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public static function search(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => ClienteService::buscar($req->query['q'] ?? ''), 'success' => true]);
    }

    public static function detalle(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        try {
            Response::json(['data' => ClienteService::detalle((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }

    public static function operaciones(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => ClienteService::operaciones((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
    }
}
