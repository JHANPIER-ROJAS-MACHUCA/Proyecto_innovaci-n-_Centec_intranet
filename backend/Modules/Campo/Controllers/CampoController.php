<?php
// Módulo Campo — controlador (extraído de controllers/CampoController.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/../Services/CampoService.php';
require_once __DIR__ . '/../../Caja/Services/CajaService.php';
require_once __DIR__ . '/../../../repositories/FinancieraRepository.php';

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
