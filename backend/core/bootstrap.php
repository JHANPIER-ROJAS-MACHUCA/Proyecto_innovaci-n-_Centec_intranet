<?php
use Illuminate\Database\Capsule\Manager as Capsule;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Lima');

require_once __DIR__ . '/../utils/Delay.php';
require_once __DIR__ . '/../utils/Holiday.php';
foreach (glob(__DIR__ . '/../models/*.php') as $f) require_once $f;

global $capsule;
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => $_ENV['DB_CONNECTION'] ?? 'mysql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'database' => $_ENV['DB_DATABASE'] ?? '',
    'username' => $_ENV['DB_USERNAME'] ?? '',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8', 'collation' => 'utf8_unicode_ci', 'prefix' => '', 'strict' => false,
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
