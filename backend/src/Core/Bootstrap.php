<?php

namespace CrediSoporte\Core;

use Illuminate\Database\Capsule\Manager as Capsule;

// Arranque unico: autoload + .env + zona horaria + Eloquent.
// Reutiliza las mismas variables que app/database/database.php.
class Bootstrap
{
    protected static $booted = false;
    protected static $capsule = null;

    // Raiz del backend (equivale al __DIR__.'/..' de los scripts legacy).
    public static function root(): string
    {
        return dirname(__DIR__, 2);
    }

    // Capsule Manager: reemplaza la variable $database de app/database/database.php.
    public static function capsule(): Capsule
    {
        if (static::$capsule === null) {
            $config = static::config();
            $capsule = new Capsule;
            $capsule->addConnection($config['database']);
            $capsule->setAsGlobal();
            $capsule->bootEloquent();
            static::$capsule = $capsule;
        }
        return static::$capsule;
    }

    public static function boot(string $basePath): array
    {
        if (static::$booted) {
            return static::config();
        }

        $dotenv = \Dotenv\Dotenv::createImmutable($basePath);
        $dotenv->safeLoad();

        $config = static::config();

        date_default_timezone_set($config['app']['timezone']);

        static::capsule();

        static::$booted = true;

        return $config;
    }

    public static function config(): array
    {
        $root = dirname(__DIR__, 2);
        return [
            'app' => require $root . '/config/app.php',
            'database' => require $root . '/config/database.php',
            'cors' => require $root . '/config/cors.php',
        ];
    }
}
