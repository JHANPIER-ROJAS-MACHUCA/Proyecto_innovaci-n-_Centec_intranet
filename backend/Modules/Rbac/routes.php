<?php
// Módulo Rbac — rutas (contratos idénticos a legacy; phase2-modular).
return function (Router $r) {
    $r->add('GET', '/api/rbac/mis-permisos', RbacController::class, 'misPermisos');
    $r->add('GET', '/api/rbac/matriz', RbacController::class, 'matriz');
    $r->add('GET', '/api/rbac/roles', RbacController::class, 'roles');
    $r->add('POST', '/api/rbac/asignar', RbacController::class, 'asignar');
    $r->add('POST', '/api/rbac/permiso-estado', RbacController::class, 'permisoEstado');
};
