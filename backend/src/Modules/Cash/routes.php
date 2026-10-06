<?php

// Rutas del modulo Cash (cajas y movimientos). $router es CrediSoporte\Core\Router.

use CrediSoporte\Core\Response;
use CrediSoporte\Modules\Cash\CashService;

$router->get('/cajas', function () {
    Response::json((new CashService())->oficinas()->toArray());
});

$router->get('/movimientos', function () {
    Response::json((new CashService())->movimientos()->toArray());
});
