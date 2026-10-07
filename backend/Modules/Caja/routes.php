<?php
// Módulo Caja — rutas (contratos idénticos a legacy; phase2-modular).
return function (Router $r) {
    $r->add('GET', '/api/caja/estado', CajaController::class, 'estado');
    $r->add('POST', '/api/caja/cerrar', CajaController::class, 'cerrar');
    $r->add('GET', '/api/caja/bloqueo', CajaController::class, 'bloqueo');
    $r->add('GET', '/api/caja/movimientos', ReporteController::class, 'movimientos');
    $r->add('POST', '/api/caja/abrir-gerencia', AperturaController::class, 'abrirGerencia');
    $r->add('POST', '/api/caja/abrir-oficina', AperturaController::class, 'abrirOficina');
    $r->add('GET', '/api/boveda/saldos', BovedaController::class, 'saldos');
    $r->add('POST', '/api/boveda/consumir', BovedaController::class, 'consumir');
    $r->add('POST', '/api/boveda/designar', BovedaController::class, 'designar');
    $r->add('POST', '/api/boveda/aceptar', BovedaController::class, 'aceptar');
    $r->add('POST', '/api/boveda/eliminar', BovedaController::class, 'eliminarAsignacion');
    $r->add('GET', '/api/boveda/pendientes', BovedaController::class, 'pendientesAceptar');
    $r->add('GET', '/api/billetaje/pendientes', BilletajeController::class, 'pendientes');
    $r->add('POST', '/api/billetaje/confirmar', BilletajeController::class, 'confirmar');
    $r->add('POST', '/api/billetaje', BilletajeController::class, 'registrar');
    $r->add('GET', '/api/recibos/motivos', ReciboController::class, 'motivos');
    $r->add('POST', '/api/recibos', ReciboController::class, 'registrar');
    $r->add('GET', '/api/extornos', ExtornoController::class, 'listar');
    $r->add('POST', '/api/extornos/resolver', ExtornoController::class, 'resolver');
    $r->add('POST', '/api/extornos', ExtornoController::class, 'solicitar');
};
