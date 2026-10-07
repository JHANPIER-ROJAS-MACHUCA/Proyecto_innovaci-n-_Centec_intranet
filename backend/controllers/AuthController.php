<?php
require_once __DIR__ . '/../services/AuthService.php';

class AuthController
{
    public static function login(AppRequest $req): void
    {
        $user = AuthService::attempt((string)($req->body['usu'] ?? ''), (string)($req->body['pas'] ?? ''));
        if (!$user) Response::legacyBool(false);
        AuthService::issueCookies($user);
        Response::legacyBool(true);
    }

    public static function logout(AppRequest $req): void
    {
        AuthService::clearCookies();
        Response::ok(null);
    }

    public static function me(AppRequest $req): void
    {
        $user = AuthMiddleware::user();
        Response::json(['data' => $user, 'success' => (bool) $user]);
    }
}
