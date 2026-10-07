<?php
// Módulo Sucursales — rutas (contratos idénticos a legacy; phase2-modular).
// Requiere que SucursalController esté cargado (lo hace routes/api.php).
return function (Router $r) {
    $r->add('GET', '/api/sucursales', SucursalController::class, 'listar');
    $r->add('GET', '/api/sucursales/home', SucursalController::class, 'home');
    $r->add('GET', '/api/sucursales/detalle', SucursalController::class, 'detalle');
    $r->add('GET', '/api/sucursales/metodos-pago', SucursalController::class, 'metodosPago');
    $r->add('GET', '/api/sucursales/deudas', SucursalController::class, 'deudas');
    $r->add('GET', '/api/sucursales/contexto-pago', SucursalController::class, 'contextoPago');
    $r->add('GET', '/api/sucursales/comprobantes', SucursalController::class, 'comprobantes');
    $r->add('POST', '/api/sucursales/horario-pago', SucursalController::class, 'horarioPago');
    $r->add('POST', '/api/sucursales/contacto', SucursalController::class, 'contacto');
};
