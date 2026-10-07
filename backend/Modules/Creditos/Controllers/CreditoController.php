<?php
// Módulo Creditos — controlador (movido de controllers/, phase2-modular; idéntico).
require_once __DIR__ . '/../Services/CreditoService.php';
require_once __DIR__ . '/../Services/CreditoEstadoService.php';
require_once __DIR__ . '/../../Caja/Services/CajaService.php';

class CreditoController
{
    public static function generar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        if (empty($req->body['identi']) && empty($req->body['txtmonto'])) Response::error('identi y txtmonto requeridos.', 422);
        Response::json(['data' => CreditoService::generar($req->body), 'success' => true], 201);
    }

    public static function byCustomer(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        if (empty($req->query['idCG'])) Response::error('idCG requerido.', 422);
        Response::json(['data' => CreditoRepository::byCustomer((int) $req->query['idCG']), 'success' => true]);
    }

    public static function porEstado(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        if (!isset($req->query['estado'])) Response::error('estado requerido.', 422);
        $estados = array_map('intval', explode(',', (string) $req->query['estado']));
        Response::json(['data' => CreditoRepository::byEstado(count($estados) === 1 ? $estados[0] : $estados), 'success' => true]);
    }

    public static function tipos(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => CreditoService::tipos(), 'success' => true]);
    }

    public static function editar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        try {
            CreditoService::editar($req->body);
            Response::json(['message' => 'Crédito editado.', 'success' => true]);
        } catch (DomainException $e) {
            Response::json(['message' => $e->getMessage(), 'success' => false], 422);
        }
    }

    public static function detalle(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        $c = CreditoService::detalle((int) ($req->query['id'] ?? 0));
        if (!$c) Response::error('Crédito no existe.', 404);
        Response::json(['data' => $c, 'success' => true]);
    }

    public static function confirmar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        $monto = (float) ($req->body['edit_montoa'] ?? 0);
        if ($err = CreditoEstadoService::puedeConfirmar($user, $monto)) {
            Response::json(['message' => $err, 'success' => false], 403);
        }
        if ((int) $user['tipoU'] === 1) {
            CreditoEstadoService::confirmar((int) $req->body['edit_id'], $monto, (float) ($req->body['edit_taza'] ?? 0));
            Response::json(['message' => 'Credito aprobado.', 'success' => true]);
        }
        CajaService::exigir($user);
        CreditoEstadoService::confirmar((int) $req->body['edit_id'], $monto, (float) ($req->body['edit_taza'] ?? 0));
        Response::json(['message' => 'Credito aprobado.', 'success' => true]);
    }

    public static function cambiarEstado(AppRequest $req, int $estado): void
    {
        RoleMiddleware::require([8, 5]);
        if (empty($req->body['idP'])) Response::error('idP requerido.', 422);
        CreditoEstadoService::cambiarEstado((int) $req->body['idP'], $estado);
        Response::json(['success' => true]);
    }

    public static function activar(AppRequest $req): void
    {
        self::cambiarEstado($req, 3);
    }

    public static function desembolsar(AppRequest $req): void
    {
        self::cambiarEstado($req, 4);
    }

    public static function cancelar(AppRequest $req): void
    {
        self::cambiarEstado($req, 6);
    }
}
