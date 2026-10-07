<?php
// Módulo Reportes — rutas (contratos idénticos a legacy; phase2-modular).
return function (Router $r) {
    $r->add('GET', '/api/reportes/moras-dias', ReporteController::class, 'morasPorDias');
    $r->add('GET', '/api/reportes/sentinel', ReporteController::class, 'sentinel');
    $r->add('GET', '/api/reportes/cancelados', ReporteController::class, 'cancelados');
    $r->add('GET', '/api/reportes/sin-creditos', ReporteController::class, 'sinCreditos');
    $r->add('GET', '/api/reportes/vinculaciones', ReporteController::class, 'vinculaciones');
    $r->add('GET', '/api/reportes/proyecciones', ReporteController::class, 'proyecciones');
    $r->add('GET', '/api/reportes/ahorros-fecha', ReporteController::class, 'ahorrosPorFecha');
    $r->add('GET', '/api/reportes/eliminados', ReporteController::class, 'eliminados');
    $r->add('GET', '/api/reportes/cobros', ReporteController::class, 'cobrosPorFecha');
    $r->add('GET', '/api/reportes/desembolsos', ReporteController::class, 'desembolsosPorFecha');
    $r->add('GET', '/api/reportes/cierre', ReporteController::class, 'cierre');
    $r->add('GET', '/api/cobros/avance', AvanceController::class, 'ver');
    $r->add('POST', '/api/cobros/avance/guardar', AvanceController::class, 'guardar');
    $r->add('GET', '/api/cobros/avance/snapshot', AvanceController::class, 'snapshot');
    $r->add('GET', '/api/gerencia/sucursales', GerenciaController::class, 'sucursales');
};
