<?php
// Módulo Creditos — mantenimiento de moras (extraído de controllers/CampoController.php,
// phase2-modular; idéntico). Ambas acciones delegan en MoraService del módulo.
require_once __DIR__ . '/../Services/MoraService.php';

class MantenimientoController
{
    public static function limpiarMoras(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        Response::json(['data' => ['limpiados' => MoraService::limpiarHuerfanas()], 'success' => true]);
    }

    public static function condonarTodas(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        try {
            MoraService::condonarTodas((int) ($req->body['creditId'] ?? 0), $req->body['dates'] ?? []);
            Response::json(['success' => true], 201);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }
}
