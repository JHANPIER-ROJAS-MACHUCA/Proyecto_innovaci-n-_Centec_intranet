<?php
require_once __DIR__ . '/../services/UsuarioService.php';
require_once __DIR__ . '/../repositories/UserRepository.php';

class UsuarioController
{
    public static function list(AppRequest $req): void
    {
        // Spec: TI gestiona usuarios; Admin Sucursal ve su personal
        RoleMiddleware::require([5, 1]);
        Response::json(['data' => UsuarioService::listar(), 'success' => true]);
    }

    public static function create(AppRequest $req): void
    {
        // Spec: Gestión de Usuarios = TI
        RoleMiddleware::require([1]);
        $errors = UsuarioService::validate($req->body);
        if ($errors) Response::json(['errors' => $errors, 'message' => 'Error de validación.', 'success' => false], 422);
        Response::json(['data' => UsuarioService::create($req->body), 'success' => true], 201);
    }

    public static function update(AppRequest $req): void
    {
        RoleMiddleware::require([1]);
        try {
            UsuarioService::update((int) ($req->body['idU'] ?? 0), $req->body);
            Response::json(['success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    public static function toggleEstado(AppRequest $req): void
    {
        RoleMiddleware::require([1]);
        try {
            Response::json(['data' => UsuarioService::toggleEstado((int) ($req->body['idU'] ?? 0)), 'success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 404);
        }
    }

    public static function resetPassword(AppRequest $req): void
    {
        RoleMiddleware::require([1]);
        if (empty($req->body['idU'])) Response::error('idU requerido.', 422);
        UserRepository::resetPassword((int) $req->body['idU']);
        Response::json(['success' => true]);
    }

    public static function avatar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        if (empty($_FILES['file'])) Response::error('Archivo requerido.', 422);
        $name = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', basename($_FILES['file']['name']));
        $dest = dirname(__DIR__) . '/storage/profiles/' . $name;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) Response::error('No se pudo guardar.', 500);
        UsuarioService::setAvatar((int) $user['idU'], $name);
        Response::json(['data' => ['img' => $name], 'success' => true]);
    }

    public static function eliminarAvatar(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        UsuarioService::eliminarAvatar((int) $user['idU']);
        Response::json(['success' => true]);
    }

    public static function miPerfil(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        UsuarioService::updateMiPerfil((int) $user['idU'], $req->body);
        Response::json(['success' => true]);
    }

    public static function cambiarClave(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        try {
            UsuarioService::cambiarClave((int) $user['idU'], $req->body['actual'] ?? '', $req->body['nueva'] ?? '');
            Response::json(['success' => true]);
        } catch (DomainException $e) {
            Response::error($e->getMessage(), 403);
        }
    }
}
