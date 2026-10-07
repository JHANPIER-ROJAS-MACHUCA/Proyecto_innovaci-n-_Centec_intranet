<?php
// Módulo Catalogos — rutas (contratos idénticos a legacy; phase2-modular).
return function (Router $r) {
    $r->add('POST', '/api/metas/eliminar', MetaController::class, 'eliminar');
    $r->add('GET', '/api/metas', MetaController::class, 'listar');
    $r->add('POST', '/api/metas/actualizar', MetaController::class, 'actualizar');
    $r->add('POST', '/api/metas', MetaController::class, 'crear');
    $r->add('GET', '/api/metas/por-usuario', MetaController::class, 'porUsuario');
    $r->add('POST', '/api/justificaciones/eliminar', JustificacionController::class, 'eliminar');
    $r->add('POST', '/api/justificaciones/actualizar', JustificacionController::class, 'actualizar');
    $r->add('POST', '/api/justificaciones', JustificacionController::class, 'crear');
    $r->add('GET', '/api/justificaciones', JustificacionController::class, 'listar');
    $r->add('POST', '/api/adjuntos/subir', AdjuntoController::class, 'subir');
    $r->add('POST', '/api/adjuntos/eliminar', AdjuntoController::class, 'eliminar');
    $r->add('POST', '/api/adjuntos', AdjuntoController::class, 'crear');
    $r->add('GET', '/api/adjuntos', AdjuntoController::class, 'listar');
    $r->add('GET', '/api/relaciones', RelacionController::class, 'list');
    $r->add('POST', '/api/relaciones/eliminar', RelacionController::class, 'delete');
    $r->add('POST', '/api/relaciones', RelacionController::class, 'add');
    $r->add('GET', '/api/oficinas', OficinaController::class, 'list');
    $r->add('GET', '/api/empresa', EmpresaController::class, 'ver');
    $r->add('POST', '/api/empresa/actualizar', EmpresaController::class, 'actualizar');
};
