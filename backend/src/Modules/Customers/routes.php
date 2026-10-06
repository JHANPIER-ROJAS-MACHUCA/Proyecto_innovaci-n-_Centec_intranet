<?php

// Rutas del modulo Customers. $router es CrediSoporte\Core\Router.

use CrediSoporte\Core\Response;
use CrediSoporte\Modules\Customers\CustomerService;

$router->get('/clientes', function () {
    Response::json((new CustomerService())->recientes()->toArray());
});

$router->get('/clientes/:id', function ($params) {
    $customer = (new CustomerService())->find((int) $params['id']);
    if (!$customer) {
        Response::notFound('Cliente no encontrado.');
        return;
    }
    Response::json($customer->toArray());
});
