<?php
// Módulo Clientes — rutas (contratos idénticos a legacy; phase2-modular).
return function (Router $r) {
    $r->add('GET', '/api/clientes/search', ClienteController::class, 'search');
    $r->add('GET', '/api/clientes/operaciones', ClienteController::class, 'operaciones');
    $r->add('GET', '/api/clientes/detalle', ClienteController::class, 'detalle');
    $r->add('POST', '/api/clientes/actualizar', ClienteController::class, 'update');
    $r->add('POST', '/api/clientes', ClienteController::class, 'create');
    $r->add('POST', '/api/clientes/registro-completo', RegistroClienteController::class, 'crearCompleto');
    $r->add('GET', '/api/clientes/ficha-completa', RegistroClienteController::class, 'fichaCompleta');
    $r->add('GET', '/api/clientes/clasificacion', ClasificacionController::class, 'listar');
    $r->add('POST', '/api/clientes/sentinel', ClasificacionController::class, 'setSentinel');
    $r->add('GET', '/api/clientes/morosidad', ClasificacionController::class, 'morosidad');
    $r->add('GET', '/api/cartas/invitacion', CartaController::class, 'invitacion');
    $r->add('GET', '/api/cartas/cobranza', CartaController::class, 'cobranza');
    $r->add('POST', '/api/cartas/registrar', CartaController::class, 'registrar');
    $r->add('GET', '/api/cartas/historial', CartaController::class, 'historial');
};
