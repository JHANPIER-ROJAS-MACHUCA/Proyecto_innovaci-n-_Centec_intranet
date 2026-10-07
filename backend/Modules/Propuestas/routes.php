<?php
// Módulo Propuestas — rutas (contratos idénticos a legacy; phase2-modular).
return function (Router $r) {
    $r->add('GET', '/api/propuestas/detalle', PropuestaController::class, 'detalle');
    $r->add('GET', '/api/propuestas', PropuestaController::class, 'list');
    $r->add('POST', '/api/propuestas/responder', PropuestaController::class, 'responder');
    $r->add('POST', '/api/propuestas/eliminar', PropuestaController::class, 'eliminar');
    $r->add('POST', '/api/propuestas/evaluar', PropuestaController::class, 'evaluar');
    $r->add('GET', '/api/propuestas/detalle', PropuestaController::class, 'detalle');
    $r->add('POST', '/api/propuestas/crear', PropuestaController::class, 'crear');
};
