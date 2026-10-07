<?php
// NOTA phase2: CampoController migró a Modules/Campo/Controllers/;
// FormatoController a Modules/Creditos/Controllers/. Aquí quedan Dni/Ubigeo (shared).
require_once __DIR__ . '/../Modules/Caja/Services/CajaService.php';
require_once __DIR__ . '/../services/PanelService.php';

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

// NOTA phase2: FormatoController migró a Modules/Creditos/Controllers/.
