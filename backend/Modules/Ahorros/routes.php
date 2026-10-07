<?php
// Módulo Ahorros — rutas (contratos idénticos a legacy; phase2-modular).
return function (Router $r) {
    $r->add('POST', '/api/ahorros/anular', AhorroController::class, 'anular');
    $r->add('POST', '/api/ahorros', AhorroController::class, 'registrar');
    $r->add('GET', '/api/ahorros/detalle', AhorroController::class, 'detalle');
};
