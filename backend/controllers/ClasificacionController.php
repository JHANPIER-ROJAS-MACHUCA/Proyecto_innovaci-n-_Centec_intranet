<?php
require_once __DIR__ . '/../services/ClasificacionService.php';

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

class CartaController
{
    public static function invitacion(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1]);
        try {
            Response::json(['data' => CartaService::invitacion((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }

    public static function cobranza(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1, 2, 9]);
        try {
            Response::json(['data' => CartaService::cobranza((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }

    public static function registrar(AppRequest $req): void
    {
        $user = RoleMiddleware::require([8, 5, 1, 2, 9]);
        try {
            $id = CartaService::registrar(
                (int) ($req->body['idCG'] ?? 0), (string) ($req->body['tipo'] ?? ''),
                $req->body['tramo'] ?? null, (string) ($req->body['titulo'] ?? ''),
                (string) ($req->body['contenido'] ?? ''), (int) $user['idU']
            );
            Response::json(['data' => ['id' => $id], 'success' => true], 201);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public static function historial(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5, 1, 2, 9]);
        Response::json(['data' => CartaService::historial((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
    }
}
