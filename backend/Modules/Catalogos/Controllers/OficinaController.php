<?php
// Módulo Catalogos — controlador de oficinas (extraído de
// controllers/CatalogoController.php, phase2-modular; idéntico).
require_once __DIR__ . '/../Services/OficinaService.php';

class OficinaController
{
    public static function list(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => OficinaService::listar(), 'success' => true]);
    }
}
