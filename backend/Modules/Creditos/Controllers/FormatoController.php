<?php
// Módulo Creditos — controlador de formato de contrato (extraído de
// controllers/CampoController.php, phase2-modular; idéntico).
require_once __DIR__ . '/../Services/FormatoService.php';

class FormatoController
{
    public static function contrato(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        try {
            Response::json(['data' => FormatoService::contrato((int) ($req->query['idP'] ?? 0)), 'success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }
}
