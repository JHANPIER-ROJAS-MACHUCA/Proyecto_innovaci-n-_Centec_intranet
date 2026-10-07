<?php
// Módulo Campo — rutas (contratos idénticos a legacy; phase2-modular).
return function (Router $r) {
    $r->add('GET', '/api/campo/credit-to-pay', CampoController::class, 'creditToPay');
    $r->add('GET', '/api/campo/cobros-hoy', CampoController::class, 'cobrosHoy');
    $r->add('GET', '/api/campo/cobros-realizados', CampoController::class, 'cobrosRealizados');
};
