<?php
require_once __DIR__ . '/../services/CajaService.php';
require_once __DIR__ . '/../services/PanelService.php';
require_once __DIR__ . '/../Modules/Creditos/Services/MoraService.php';

class CampoController
{
    public static function cobrosHoy(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        $out = CampoService::cobrosHoy($user, trim($req->query['search'] ?? ''));
        Response::json(['data' => $out['data'], 'charges' => $out['charges'], 'success' => true]);
    }

    public static function creditToPay(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        try {
            $out = CampoService::creditToPay((int) ($req->query['creditId'] ?? 0));
            Response::json(['data' => $out['data'], 'cuotas' => $out['cuotas'], 'totalPendiente' => $out['totalPendiente'], 'success' => true]);
        } catch (DomainException $e) {
            Response::json(['message' => $e->getMessage(), 'success' => false], 404);
        }
    }

    // Spec #20b: en campo la lista muestra solo cobros (tipo=3) de su caja
    public static function cobrosRealizados(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        $caja = CajaService::habilitada($user);
        if (!$caja) Response::json(['data' => [], 'success' => true]);
        Response::json(['data' => TransaccionRepository::cobrosDeCaja($caja->idCA), 'success' => true]);
    }
}

class DniController
{
    public static function consultar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        try {
            Response::json(['data' => DniService::consultar($req->query['number'] ?? ''), 'success' => true]);
        } catch (DomainException $e) {
            $code = str_contains($e->getMessage(), 'configurado') ? 503 : 422;
            Response::json(['message' => $e->getMessage(), 'success' => false], $code);
        }
    }
}

class UbigeoController
{
    public static function departamentos(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => UbigeoService::departamentos(), 'success' => true]);
    }

    public static function provincias(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        $id = isset($req->query['department_id']) && $req->query['department_id'] !== '' ? (int) $req->query['department_id'] : null;
        Response::json(['data' => UbigeoService::provincias($id), 'success' => true]);
    }

    public static function distritos(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        $id = isset($req->query['province_id']) && $req->query['province_id'] !== '' ? (int) $req->query['province_id'] : null;
        Response::json(['data' => UbigeoService::distritos($id), 'success' => true]);
    }
}

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
