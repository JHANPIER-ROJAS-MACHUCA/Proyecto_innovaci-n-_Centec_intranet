<?php
// Módulo Creditos — controlador de cobros (movido de controllers/, phase2-modular).
require_once __DIR__ . '/../Services/CobroService.php';
require_once __DIR__ . '/../../../services/ReporteService.php';

class CobroController
{
    public static function simular(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        if ($err = CobroService::validar($req->body)) Response::json($err, 422);
        CajaService::exigir($user);
        $cred = CobroService::creditoPendiente((int) $req->body['creditId']);
        if (!$cred) Response::json(['message' => 'El credito no existe.', 'success' => false], 404);
        try {
            $calc = CobroService::calcular($cred, $req->body['paymentType'] ?? 'amount', (float) ($req->body['value'] ?? 0), (float) ($req->body['mora'] ?? 0));
            Response::json(['penalty' => $calc['penalty'], 'debtCapital' => $calc['debtCapital'], 'debtInterest' => $calc['debtInterest'], 'resumen' => $calc['summaries'], 'success' => true]);
        } catch (CobroValidationException $e) {
            Response::json($e->payload, 422);
        }
    }

    public static function cobrar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        if ($err = CobroService::validar($req->body)) Response::json($err, 422);
        $caja = CajaService::exigirSinBloqueo($user);
        $cred = CobroService::creditoPendiente((int) $req->body['creditId']);
        if (!$cred) Response::json(['message' => 'El credito no existe.', 'success' => false], 404);
        try {
            $out = CobroService::ejecutar($cred, $caja, $req->body['paymentType'] ?? 'amount', (float) ($req->body['value'] ?? 0), (float) ($req->body['mora'] ?? 0), (int) $user['idU']);
            Response::json($out, 201);
        } catch (CobroValidationException $e) {
            Response::json($e->payload, 422);
        }
    }

    public static function condonar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        if (empty($req->body['creditId'])) Response::error('creditId requerido.', 422);
        MoraService::condonar((int) $req->body['creditId'], $req->body['date'] ?? date('Y-m-d'));
        Response::json(['success' => true]);
    }

    public static function eliminarTransaccion(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        TransaccionService::eliminar((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }
}
