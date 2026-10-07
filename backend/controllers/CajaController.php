<?php
require_once __DIR__ . '/../services/CajaService.php';
require_once __DIR__ . '/../services/ReporteService.php';

class CajaController
{
    public static function estado(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        $caja = CajaService::habilitada($user);
        $resumen = $caja ? CajaService::resumen($caja) : ['efectivo' => 0, 'digital' => 0];
        Response::json(['data' => array_merge(
            ['habilitada' => (bool) $caja, 'fecha' => date('d/m/Y H:i:s')],
            $resumen,
            ReporteService::conteosPanel()
        ), 'success' => true]);
    }

    public static function cerrar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        try {
            CajaService::cerrar((int) $user['idU'], $req->body['monto'] ?? null);
            Response::json(['success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    // Spec #29: estado de bloqueo del usuario por billetaje sin verificar
    public static function bloqueo(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        $b = CajaService::bloqueoBilletaje((int) $user['idU']);
        Response::json(['data' => ['bloqueado' => (bool) $b, 'billetaje' => $b], 'success' => true]);
    }
}

class BovedaController
{
    public static function saldos(AppRequest $req): void
    {
        RoleMiddleware::require([8, 1]);
        Response::json(['data' => BovedaService::saldos(), 'success' => true]);
    }

    public static function designar(AppRequest $req): void
    {
        $user = RoleMiddleware::require([8, 1]);
        try {
            $id = BovedaService::designar((int) $user['idU'], $req->body);
            Response::json(['data' => ['id' => $id], 'success' => true], 201);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public static function consumir(AppRequest $req): void
    {
        RoleMiddleware::require([8, 1]);
        BovedaService::consumir((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }

    // Spec #28: el usuario acepta su asignación (abre caja + acepta billetaje)
    public static function aceptar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        try {
            BovedaRepository::aceptar((int) ($req->body['id'] ?? 0), (int) $user['idU']);
            Response::json(['success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    // Spec #28: el administrador elimina el monto no aceptado
    public static function eliminarAsignacion(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        try {
            BovedaRepository::eliminarPendiente((int) ($req->body['id'] ?? 0));
            Response::json(['success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public static function pendientesAceptar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        $rol = (int) $user['tipoU'];
        $idU = in_array($rol, [8, 5, 1], true) && empty($req->query['propias']) ? null : (int) $user['idU'];
        Response::json(['data' => BovedaRepository::pendientesDeAceptar($idU), 'success' => true]);
    }
}
