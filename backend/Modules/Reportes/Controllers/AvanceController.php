<?php
// Módulo Reportes — controlador de avance (extraído de Services/AvanceService.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/../Services/AvanceService.php';

class AvanceController
{
    public static function ver(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1, 2, 9]);
        try {
            $idU = isset($req->query['idU']) && $req->query['idU'] !== '' ? (int) $req->query['idU'] : null;
            Response::json(['data' => AvanceService::delDia($req->query['fecha'] ?? date('Y-m-d'), $idU), 'success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public static function guardar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        try {
            $n = AvanceService::guardarSnapshot($req->body['fecha'] ?? date('Y-m-d'), isset($req->body['idU']) ? (int) $req->body['idU'] : null);
            Response::json(['data' => ['filas' => $n], 'success' => true], 201);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public static function snapshot(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1, 2, 9]);
        Response::json(['data' => AvanceService::snapshot($req->query['fecha'] ?? date('Y-m-d')), 'success' => true]);
    }
}
