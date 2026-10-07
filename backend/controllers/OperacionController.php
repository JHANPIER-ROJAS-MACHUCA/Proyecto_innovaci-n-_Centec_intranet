<?php
// NOTA phase2: AhorroController migró a Modules/Ahorros/Controllers/.
require_once __DIR__ . '/../services/CatalogoService.php';

class MetaController
{
    public static function listar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => MetaService::listar(), 'success' => true]);
    }

    public static function crear(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        Response::json(['data' => MetaService::crear($req->body), 'success' => true], 201);
    }

    public static function actualizar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        MetaService::actualizar((int) ($req->body['id'] ?? 0), $req->body);
        Response::json(['success' => true]);
    }

    public static function eliminar(AppRequest $req): void
    {
        RoleMiddleware::require([8, 5]);
        MetaService::eliminar((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }

    public static function porUsuario(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => MetaService::porUsuario(), 'success' => true]);
    }
}

class JustificacionController
{
    public static function listar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        $cid = isset($req->query['credit_id']) && $req->query['credit_id'] !== '' ? (int) $req->query['credit_id'] : null;
        Response::json(['data' => JustificacionService::listar($cid), 'success' => true]);
    }

    public static function crear(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => JustificacionService::crear($req->body), 'success' => true], 201);
    }

    public static function actualizar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        JustificacionService::actualizar((int) ($req->body['id'] ?? 0), $req->body);
        Response::json(['success' => true]);
    }

    public static function eliminar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        JustificacionService::eliminar((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }
}

class AdjuntoController
{
    public static function listar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => AdjuntoService::listar((int) ($req->query['customer_id'] ?? 0)), 'success' => true]);
    }

    public static function crear(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => AdjuntoService::crear($req->body), 'success' => true], 201);
    }

    public static function eliminar(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        AdjuntoService::eliminar((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }

    public static function subir(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        if (empty($_FILES['file'])) Response::error('Archivo requerido.', 422);
        try {
            $name = AdjuntoService::guardarArchivo($_FILES['file']);
            Response::json(['data' => ['name' => $name], 'success' => true], 201);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
