<?php

// Rutas del modulo Users. $router es CrediSoporte\Core\Router.

use CrediSoporte\Core\Response;
use CrediSoporte\Modules\Users\UserService;

$router->get('/usuarios', function () {
    Response::json((new UserService())->activos()->toArray());
});

$router->get('/usuarios/:id', function ($params) {
    $user = (new UserService())->find((int) $params['id']);
    if (!$user) {
        Response::notFound('Usuario no encontrado.');
        return;
    }
    Response::json($user->toArray());
});
