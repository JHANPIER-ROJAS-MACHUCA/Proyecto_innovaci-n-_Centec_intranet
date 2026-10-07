<?php
// Módulo Reportes — controlador (movido de controllers/, phase2-modular; idéntico).
require_once __DIR__ . '/../Services/ReporteService.php';
require_once __DIR__ . '/../../Caja/Services/CajaService.php';

class ReporteController
{
    private static function rango(AppRequest $req): array
    {
        $hoy = date('Y-m-d');
        return [$req->query['desde'] ?? $hoy, $req->query['hasta'] ?? $hoy];
    }

    public static function cobrosPorFecha(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        [$desde, $hasta] = self::rango($req);
        $idU = isset($req->query['idU']) && $req->query['idU'] !== '' ? (int) $req->query['idU'] : null;
        Response::json(['data' => ReporteService::cobrosPorFecha($desde, $hasta, $idU), 'success' => true]);
    }

    public static function desembolsosPorFecha(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        [$desde, $hasta] = self::rango($req);
        $idU = isset($req->query['idU']) && $req->query['idU'] !== '' ? (int) $req->query['idU'] : null;
        Response::json(['data' => ReporteService::desembolsosPorFecha($desde, $hasta, $idU), 'success' => true]);
    }

    public static function cierre(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => ReporteService::cierre($req->query['mes'] ?? date('Y-m')), 'success' => true]);
    }

    public static function movimientos(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        $caja = CajaService::habilitada($user);
        if (!$caja) Response::json(['data' => [], 'success' => true]);
        Response::json(['data' => TransaccionService::movimientos($caja->idCA), 'success' => true]);
    }

    public static function morasPorDias(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => ReporteService::morasPorDias(), 'success' => true]);
    }

    public static function sentinel(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => ReporteService::sentinel(), 'success' => true]);
    }

    public static function cancelados(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => ReporteService::cancelados(), 'success' => true]);
    }

    public static function sinCreditos(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => ReporteService::sinCreditos(), 'success' => true]);
    }

    public static function vinculaciones(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => ReporteService::vinculaciones($req->query['titular'] ?? null), 'success' => true]);
    }

    public static function proyecciones(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => ReporteService::proyecciones(
            $req->query['desde'] ?? date('Y-m-d', strtotime('-30 days')),
            $req->query['hasta'] ?? date('Y-m-d')
        ), 'success' => true]);
    }

    public static function ahorrosPorFecha(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        [$desde, $hasta] = self::rango($req);
        Response::json(['data' => ReporteService::ahorrosPorFecha($desde, $hasta), 'success' => true]);
    }

    public static function eliminados(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        $page = max(1, (int) ($req->query['page'] ?? 1));
        Response::json(['data' => ReporteService::eliminados($page), 'page' => $page, 'success' => true]);
    }
}
