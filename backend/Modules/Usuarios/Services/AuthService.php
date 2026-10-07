<?php
// Módulo Usuarios — autenticación (movido de services/, phase2-modular; idéntico).
require_once __DIR__ . '/../Repositories/UserRepository.php';
require_once __DIR__ . '/../../../utils/Password.php';

class AuthService
{
    public static function attempt(string $usu, string $pas): ?array
    {
        $user = UserRepository::findActiveByDni($usu);
        if (!$user) return null;
        if (!Password::verify($pas, $user->pass)) return null;
        return $user->toArray();
    }

    public static function issueCookies(array $user): void
    {
        // Cookie tuser = idRol CENTECPC (ver AuthMiddleware).
        $exp = time() + 86400;
        setcookie('user1', $user['idU'], $exp, '/', '');
        setcookie('nombre_U', trim(($user['apU'] ?? '') . ' ' . ($user['amU'] ?? '') . ' ' . ($user['nomU'] ?? '')), $exp, '/', '');
        setcookie('tuser', $user['idRol'], $exp, '/', '');
        setcookie('tofi', $user['idO'] ?? ($_COOKIE['tofi'] ?? ''), $exp, '/', '');
    }

    public static function clearCookies(): void
    {
        foreach (['user1', 'nombre_U', 'tuser', 'tofi'] as $c) setcookie($c, '', time() - 3600, '/', '');
    }
}
