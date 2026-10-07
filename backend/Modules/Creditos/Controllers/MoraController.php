<?php
// Módulo Creditos — controlador de moras (extraído de controllers/CatalogoController.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/../Services/MoraService.php';

class MoraController
{
    public static function deudores(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => MoraService::deudores(), 'success' => true]);
    }
}
