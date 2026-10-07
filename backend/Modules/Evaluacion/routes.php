<?php
// Módulo Evaluacion — rutas (contratos idénticos a legacy; phase2-modular).
return function (Router $r) {
    $r->add('GET', '/api/evaluaciones', EvaluacionController::class, 'listar');
    $r->add('GET', '/api/evaluaciones/ver', EvaluacionController::class, 'ver');
    $r->add('GET', '/api/evaluaciones/historial', EvaluacionController::class, 'historial');
    $r->add('POST', '/api/evaluaciones/guardar', EvaluacionController::class, 'guardar');
    $r->add('POST', '/api/evaluaciones/eliminar', EvaluacionController::class, 'eliminar');
    $r->add('GET', '/api/evaluaciones/buscar-cliente', EvaluacionController::class, 'buscarCliente');
    $r->add('GET', '/api/documentos/evaluacion-pdf', DocumentoController::class, 'evaluacionPdf');
    $r->add('GET', '/api/documentos/evaluacion-patrimonio', DocumentoController::class, 'evaluacionPatrimonio');
    $r->add('GET', '/api/documentos/evaluacion-word', DocumentoController::class, 'evaluacionWord');
    $r->add('GET', '/api/documentos/cliente-ficha', DocumentoController::class, 'clienteFicha');
    $r->add('GET', '/api/documentos/formatos', DocumentoController::class, 'formatos');
};
