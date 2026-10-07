<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->safeLoad();

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Lima');

return [
    'app_name' => $_ENV['APP_NAME'] ?? 'CENTECP',
    'app_url'  => $_ENV['APP_URL'] ?? 'http://localhost',
    'api_path' => $_ENV['API_PATH'] ?? '/api',
];
