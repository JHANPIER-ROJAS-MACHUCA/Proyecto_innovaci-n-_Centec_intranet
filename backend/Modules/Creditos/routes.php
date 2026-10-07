<?php
// Módulo Creditos — rutas (contratos idénticos a legacy; phase2-modular).
// Incluye cobros de cuotas (CobroController). Se deja fuera a propósito:
// - POST /api/cobros/condonar-todas (MantenimientoController, compartido)
// - mora/condonaciones (MoraService/Repository, compartidos hasta Modules/Caja)
// - propuestas (PropuestaService, compartido hasta su split)
return function (Router $r) {
    $r->add('POST', '/api/creditos/generar', CreditoController::class, 'generar');
    $r->add('GET', '/api/creditos/por-cliente', CreditoController::class, 'byCustomer');
    $r->add('GET', '/api/creditos/detalle', CreditoController::class, 'detalle');
    $r->add('POST', '/api/creditos/confirmar', CreditoController::class, 'confirmar');
    $r->add('POST', '/api/creditos/activar', CreditoController::class, 'activar');
    $r->add('POST', '/api/creditos/desembolsar', CreditoController::class, 'desembolsar');
    $r->add('POST', '/api/creditos/cancelar', CreditoController::class, 'cancelar');
    $r->add('GET', '/api/creditos/tipos', CreditoController::class, 'tipos');
    $r->add('POST', '/api/creditos/editar', CreditoController::class, 'editar');
    $r->add('GET', '/api/creditos', CreditoController::class, 'porEstado');
    $r->add('POST', '/api/cobros', CobroController::class, 'cobrar');
    $r->add('POST', '/api/cobros/simular', CobroController::class, 'simular');
    $r->add('POST', '/api/cobros/condonar', CobroController::class, 'condonar');
    $r->add('POST', '/api/cobros/eliminar', CobroController::class, 'eliminarTransaccion');
    $r->add('POST', '/api/cobros/simular', CobroController::class, 'simular');
    $r->add('GET', '/api/mora/deudores', MoraController::class, 'deudores');
    $r->add('POST', '/api/mantenimiento/limpiar-moras', MantenimientoController::class, 'limpiarMoras');
    $r->add('POST', '/api/cobros/condonar-todas', MantenimientoController::class, 'condonarTodas');
    $r->add('GET', '/api/formatos/contrato', FormatoController::class, 'contrato');
};
