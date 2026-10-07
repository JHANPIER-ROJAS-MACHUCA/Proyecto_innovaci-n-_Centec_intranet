<?php
require_once __DIR__ . '/../Modules/Usuarios/Controllers/AuthController.php';
require_once __DIR__ . '/../Modules/Usuarios/Controllers/UsuarioController.php';
require_once __DIR__ . '/../Modules/Caja/Controllers/CajaController.php';
require_once __DIR__ . '/../Modules/Caja/Controllers/OperativaController.php';
require_once __DIR__ . '/../controllers/CatalogoController.php';
require_once __DIR__ . '/../controllers/CampoController.php';
require_once __DIR__ . '/../controllers/CpanelController.php';
require_once __DIR__ . '/../controllers/OperacionController.php';
require_once __DIR__ . '/../Modules/Evaluacion/Controllers/EvaluacionController.php';
require_once __DIR__ . '/../Modules/Evaluacion/Controllers/DocumentoController.php';
require_once __DIR__ . '/../controllers/HealthController.php';
require_once __DIR__ . '/../Modules/Sucursales/Controllers/SucursalController.php';
require_once __DIR__ . '/../Modules/Rbac/Controllers/RbacController.php';
require_once __DIR__ . '/../Modules/Clientes/Controllers/ClienteController.php';
require_once __DIR__ . '/../Modules/Clientes/Controllers/RegistroClienteController.php';
require_once __DIR__ . '/../Modules/Clientes/Controllers/ClasificacionController.php';
require_once __DIR__ . '/../Modules/Clientes/Controllers/CartaController.php';
require_once __DIR__ . '/../Modules/Creditos/Controllers/CreditoController.php';
require_once __DIR__ . '/../Modules/Creditos/Controllers/CobroController.php';
require_once __DIR__ . '/../Modules/Creditos/Controllers/MoraController.php';
require_once __DIR__ . '/../Modules/Creditos/Controllers/FormatoController.php';
require_once __DIR__ . '/../Modules/Campo/Controllers/CampoController.php';
require_once __DIR__ . '/../Modules/Propuestas/Controllers/PropuestaController.php';
require_once __DIR__ . '/../Modules/Ahorros/Controllers/AhorroController.php';
require_once __DIR__ . '/../Modules/Reportes/Controllers/ReporteController.php';
require_once __DIR__ . '/../Modules/Reportes/Controllers/AvanceController.php';
require_once __DIR__ . '/../Modules/Reportes/Controllers/GerenciaController.php';

return function (Router $r) {
    $r->add('GET', '/', HealthController::class, 'index');
    $r->add('GET', '/api', HealthController::class, 'index');
    $r->add('GET', '/api/health', HealthController::class, 'health');
    // Módulo Usuarios (auth + gestión) — ver backend/Modules/Usuarios/
    (require __DIR__ . '/../Modules/Usuarios/routes.php')($r);
    $r->add('GET', '/api/caja/estado', CajaController::class, 'estado');
    // Módulo Clientes — ver backend/Modules/Clientes/
    (require __DIR__ . '/../Modules/Clientes/routes.php')($r);
    // Módulo Créditos (cobros incluidos) — ver backend/Modules/Creditos/
    (require __DIR__ . '/../Modules/Creditos/routes.php')($r);
    // Módulo Ahorros — ver backend/Modules/Ahorros/
    (require __DIR__ . '/../Modules/Ahorros/routes.php')($r);
    $r->add('POST', '/api/metas/eliminar', MetaController::class, 'eliminar');
    $r->add('GET', '/api/metas', MetaController::class, 'listar');
    $r->add('POST', '/api/metas/actualizar', MetaController::class, 'actualizar');
    $r->add('POST', '/api/metas', MetaController::class, 'crear');
    $r->add('POST', '/api/justificaciones/eliminar', JustificacionController::class, 'eliminar');
    $r->add('POST', '/api/justificaciones/actualizar', JustificacionController::class, 'actualizar');
    $r->add('POST', '/api/justificaciones', JustificacionController::class, 'crear');
    $r->add('POST', '/api/adjuntos/subir', AdjuntoController::class, 'subir');
    $r->add('POST', '/api/adjuntos/eliminar', AdjuntoController::class, 'eliminar');
    $r->add('POST', '/api/adjuntos', AdjuntoController::class, 'crear');
    $r->add('GET', '/api/relaciones', RelacionController::class, 'list');
    $r->add('POST', '/api/relaciones/eliminar', RelacionController::class, 'delete');
    $r->add('POST', '/api/relaciones', RelacionController::class, 'add');
    $r->add('GET', '/api/oficinas', OficinaController::class, 'list');
    // Módulo Propuestas — ver backend/Modules/Propuestas/
    (require __DIR__ . '/../Modules/Propuestas/routes.php')($r);
    $r->add('POST', '/api/cobros/simular', CobroController::class, 'simular');
    $r->add('GET', '/api/adjuntos', AdjuntoController::class, 'listar');
    $r->add('POST', '/api/usuarios/avatar', UsuarioController::class, 'avatar');
    $r->add('POST', '/api/usuarios/eliminar-avatar', UsuarioController::class, 'eliminarAvatar');
    $r->add('POST', '/api/usuarios/mi-perfil', UsuarioController::class, 'miPerfil');
    $r->add('POST', '/api/usuarios/cambiar-clave', UsuarioController::class, 'cambiarClave');
    $r->add('GET', '/api/metas/por-usuario', MetaController::class, 'porUsuario');
    // Módulo Reportes — ver backend/Modules/Reportes/
    (require __DIR__ . '/../Modules/Reportes/routes.php')($r);
    // Módulo Campo — ver backend/Modules/Campo/
    (require __DIR__ . '/../Modules/Campo/routes.php')($r);
    $r->add('GET', '/api/util/dni', DniController::class, 'consultar');
    $r->add('GET', '/api/util/distritos', UbigeoController::class, 'distritos');
    $r->add('GET', '/api/util/provincias', UbigeoController::class, 'provincias');
    $r->add('GET', '/api/util/departamentos', UbigeoController::class, 'departamentos');
    $r->add('GET', '/api/cpanel/resumen', CpanelController::class, 'resumen');
    $r->add('GET', '/api/empresa', EmpresaController::class, 'ver');
    $r->add('POST', '/api/empresa/actualizar', EmpresaController::class, 'actualizar');
    // Módulo Caja (caja/bóveda/billetaje/recibos/extornos) — ver backend/Modules/Caja/
    (require __DIR__ . '/../Modules/Caja/routes.php')($r);
    $r->add('GET', '/api/justificaciones', JustificacionController::class, 'listar');
    // Módulo evaluador + documentos — ver backend/Modules/Evaluacion/
    (require __DIR__ . '/../Modules/Evaluacion/routes.php')($r);
    // Módulo sucursales — ver backend/Modules/Sucursales/
    (require __DIR__ . '/../Modules/Sucursales/routes.php')($r);
    // RBAC — ver backend/Modules/Rbac/
    (require __DIR__ . '/../Modules/Rbac/routes.php')($r);
};
