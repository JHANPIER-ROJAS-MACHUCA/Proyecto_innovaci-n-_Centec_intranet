<?php

// Front controller modular de CREDISOPORTE.
//
// - Los archivos fisicos (index.html, CSS/, JS/, app/api/*.php, ...) los sirve
//   Apache directamente gracias al .htaccess: NO se tocan.
// - Solo las rutas sin archivo fisico llegan aqui y se despachan al Router
//   con los modulos registrados en routes/api.php.
// - Reemplaza al stub anterior que apuntaba a ../laravel_credit (inexistente)
//   y producia un error fatal en cada peticion.

use CrediSoporte\Core\Bootstrap;
use CrediSoporte\Core\Response;
use CrediSoporte\Core\Router;

require __DIR__ . '/vendor/autoload.php';

$config = Bootstrap::boot(__DIR__);

// CORS basico para el SPA.
$cors = $config['cors'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $cors['allowed_origins'], true)) {
    header("Access-Control-Allow-Origin: {$origin}");
    header('Access-Control-Allow-Headers: ' . implode(', ', $cors['allowed_headers']));
    header('Access-Control-Allow-Methods: ' . implode(', ', $cors['allowed_methods']));
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$router = new Router();
require __DIR__ . '/routes/api.php';
require __DIR__ . '/routes/legacy.php';

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$path = '/' . trim(preg_replace('#^/api#', '', $uri), '/');
if ($path === '/') {
    Response::json(['name' => $config['app']['name'], 'version' => '2.0-modular'], 'API operativa.');
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!$router->dispatch($method, $path === '/' ? $path : rtrim($path, '/'))) {
    Response::notFound("Ruta {$method} {$path} no registrada.");
}
