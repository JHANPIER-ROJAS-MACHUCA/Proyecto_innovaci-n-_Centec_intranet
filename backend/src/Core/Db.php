<?php

namespace CrediSoporte\Core;

use Illuminate\Database\Capsule\Manager as Capsule;

// Acceso PDO directo para scripts (seeds, migraciones programaticas).
// Requiere Bootstrap::boot() previo.
class Db
{
    public static function pdo(): \PDO
    {
        return Capsule::connection()->getPdo();
    }

    /** @return \PDOStatement */
    public static function query(string $sql, array $params = [])
    {
        $st = static::pdo()->prepare($sql);
        $st->execute($params);
        return $st;
    }

    public static function getConnection(): \PDO
    {
        return static::pdo();
    }
}
