<?php

// Rutas del modulo Credits. $router es CrediSoporte\Core\Router.

use CrediSoporte\Core\Response;
use CrediSoporte\Modules\Credits\CreditService;

$router->get('/creditos', function () {
    Response::json((new CreditService())->recientes()->toArray());
});

$router->get('/creditos/:id', function ($params) {
    $credit = (new CreditService())->detalle((int) $params['id']);
    if (!$credit) {
        Response::notFound('Crédito no encontrado.');
        return;
    }
    Response::json($credit->toArray());
});
