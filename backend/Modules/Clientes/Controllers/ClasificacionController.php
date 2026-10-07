<?php
// Módulo Clientes — controlador de clasificación (movido de controllers/,
// phase2-modular; idéntico, incluida la fórmula de comportamiento).
require_once __DIR__ . '/../Services/ClasificacionService.php';

class ClasificacionController
{
    public static function listar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        try {
            $cat = strtoupper(trim($req->query['categoria'] ?? 'todas'));
            $limit = max(1, min(5000, (int) ($req->query['limit'] ?? 500)));
            Response::json(['data' => ClasificacionService::clasificar($cat === 'TODAS' ? 'todas' : $cat, $limit), 'success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public static function setSentinel(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        try {
            ClasificacionService::setSentinel((int) ($req->body['idCG'] ?? 0), (string) ($req->body['sentinel'] ?? ''));
            Response::json(['success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public static function morosidad(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1, 2, 9]);
        $rows = ClasificacionService::morosidad();
        foreach ($rows as &$r) $r['tramo'] = ClasificacionService::tramo((int) $r['dias_max']);
        Response::json(['data' => $rows, 'success' => true]);
    }
}
