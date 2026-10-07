<?php
// Módulo Catalogos — controlador de empresa (extraído de
// controllers/CatalogoController.php, phase2-modular; idéntico).
require_once __DIR__ . '/../Services/EmpresaService.php';

class EmpresaController
{
    public static function ver(AppRequest $req): void
    {
        // Spec: configuración estructural = TI
        RoleMiddleware::require([1]);
        Response::json(['data' => EmpresaService::ver(), 'success' => true]);
    }

    public static function actualizar(AppRequest $req): void
    {
        RoleMiddleware::require([1]);
        EmpresaService::actualizar($req->body);
        Response::json(['success' => true]);
    }
}
