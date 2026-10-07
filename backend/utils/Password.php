<?php
// Origen CENTECPC: bcrypt. Legacy Intranet: md5. Se aceptan ambos;
// los hashes nuevos/reset usan bcrypt (columna tusuarios.pass varchar(150)).
class Password
{
    public const DEFAULT = '123456';

    public static function hash(string $plain): string
    {
        return password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verify(string $plain, string $hash): bool
    {
        if (strlen($hash) >= 55 && password_verify($plain, $hash)) return true;
        return hash_equals($hash, md5($plain)); // legado
    }

    public static function defaultHash(): string
    {
        return self::hash(self::DEFAULT);
    }
}
