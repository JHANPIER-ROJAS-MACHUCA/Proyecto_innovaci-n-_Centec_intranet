<?php
// Módulo Reportes — controlador gerencial (extraído de Services/GerenciaService.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/../Services/GerenciaService.php';

class GerenciaController
{
    public static function sucursales(AppRequest $req): void
    {
        RoleMiddleware::require([8, 1]);
        $idS = isset($req->query['idS']) && $req->query['idS'] !== '' ? (int) $req->query['idS'] : null;
        Response::json(['data' => GerenciaService::porSucursal($idS), 'success' => true]);
    }
}
