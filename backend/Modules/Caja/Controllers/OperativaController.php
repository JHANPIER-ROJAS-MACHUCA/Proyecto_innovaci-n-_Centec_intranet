<?php
// Módulo Caja — aperturas, billetaje, recibos, extornos (movido, phase2-modular).
require_once __DIR__ . '/../Services/CajaService.php';
require_once __DIR__ . '/../Services/OperativaService.php';

class AperturaController
{
    public static function abrirGerencia(AppRequest $req): void
    {
        $user = RoleMiddleware::require([8]);
        try {
            $id = CajaService::abrirGerencia((int) $user['idU']);
            Response::json(['data' => ['idCO' => $id], 'success' => true], 201);
        } catch (DomainException $e) {
            Response::json(['message' => $e->getMessage(), 'success' => false], 409);
        }
    }

    public static function abrirOficina(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        try {
            $id = CajaService::abrirOficina($user);
            Response::json(['data' => ['idCO' => $id], 'success' => true], 201);
        } catch (DomainException $e) {
            Response::json(['message' => $e->getMessage(), 'success' => false], 409);
        }
    }
}

class BilletajeController
{
    public static function registrar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        CajaService::exigir($user);
        try {
            $out = BilletajeService::registrar($user, $req->body);
            Response::json(['data' => $out, 'success' => true], 201);
        } catch (DomainException $e) {
            Response::json(['message' => $e->getMessage(), 'success' => false], 409);
        }
    }

    public static function pendientes(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        Response::json(['data' => BilletajeService::pendientes(), 'success' => true]);
    }

    public static function confirmar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        BilletajeService::confirmar((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }
}

class ReciboController
{
    public static function motivos(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => ReciboService::motivos($req->query['tipoM'] ?? null), 'success' => true]);
    }

    public static function registrar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        $caja = CajaService::exigirSinBloqueo($user);
        try {
            $id = ReciboService::registrar($user, $caja, $req->body);
            Response::json(['data' => ['id' => $id], 'success' => true], 201);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }
}

class ExtornoController
{
    public static function solicitar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        try {
            ExtornoService::solicitar($user, $req->body);
            Response::json(['success' => true], 201);
        } catch (DomainException $e) {
            Response::json(['message' => $e->getMessage(), 'success' => false], 422);
        }
    }

    public static function listar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        Response::json(['data' => ExtornoService::listar(), 'success' => true]);
    }

    public static function resolver(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        try {
            ExtornoService::resolver($req->body['codigo'] ?? null);
            Response::json(['success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }
}
