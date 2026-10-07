<?php
// Módulo Ahorros — controlador (extraído de controllers/OperacionController.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/../Services/AhorroService.php';
require_once __DIR__ . '/../../Caja/Services/CajaService.php';

class AhorroController
{
    public static function registrar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        $caja = CajaService::exigirSinBloqueo($user);
        $tipo = (string) ($req->body['tipo'] ?? '');
        if (!in_array($tipo, ['7', '8'], true)) Response::error('Tipo debe ser 7 (depósito) u 8 (retiro).', 422);
        [$ahorro, $saldo] = AhorroService::saldoDisponible((int) ($req->body['customer_id'] ?? 0));
        $monto = (float) ($req->body['monto'] ?? 0);
        if ($monto <= 0 || ($tipo === '8' && $monto > $saldo)) Response::error('Monto inválido o saldo insuficiente.', 422);
        $id = AhorroService::registrar((int) $req->body['customer_id'], $tipo, (int) $req->body['motivo'], $monto, (int) $user['idU'], $caja);
        Response::json(['transactionId' => $id, 'message' => 'Registrado con exito!!', 'success' => true], 201);
    }

    public static function anular(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        try {
            AhorroService::anular((int) ($req->body['id'] ?? 0));
            Response::json(['success' => true]);
        } catch (\Throwable $th) {
            Response::error('No se pudo anular.', 500);
        }
    }

    public static function detalle(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => AhorroService::detalle((int) ($req->query['customer_id'] ?? 0)), 'success' => true]);
    }
}
