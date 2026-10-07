<?php
require_once __DIR__ . '/../services/CatalogoService.php';
require_once __DIR__ . '/../services/PanelService.php';

class RelacionController
{
    public static function list(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => RelacionService::listar((int) ($req->query['idCG'] ?? 0)), 'success' => true]);
    }

    public static function add(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        try {
            Response::json(['data' => RelacionService::agregar($req->body), 'success' => true], 201);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), strpos($e->getMessage(), 'no existe') !== false ? 404 : 422);
        }
    }

    public static function delete(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        RelacionService::eliminar((int) ($req->body['id'] ?? 0));
        Response::json(['success' => true]);
    }
}

class OficinaController
{
    public static function list(AppRequest $req): void
    {
        AuthMiddleware::requireAuth();
        Response::json(['data' => OficinaService::listar(), 'success' => true]);
    }
}

class EmpresaController
{
    public static function ver(AppRequest $req): void
    {
        // Spec: configuración estructural = TI
        RoleMiddleware::require([1]);
        Response::json(['data' => EmpresaService::ver(), 'success' => true]);
    }

    public static function actualizar(AppRequest $req): void
    {
        RoleMiddleware::require([1]);
        EmpresaService::actualizar($req->body);
        Response::json(['success' => true]);
    }
}

// NOTA phase2: PropuestaController migró a Modules/Propuestas/Controllers/.
