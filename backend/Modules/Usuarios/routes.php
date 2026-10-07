<?php
// Módulo Usuarios — rutas (contratos idénticos a legacy; phase2-modular).
return function (Router $r) {
    $r->add('POST', '/api/auth/login', AuthController::class, 'login');
    $r->add('POST', '/api/auth/logout', AuthController::class, 'logout');
    $r->add('GET', '/api/auth/me', AuthController::class, 'me');
    $r->add('GET', '/api/usuarios', UsuarioController::class, 'list');
    $r->add('POST', '/api/usuarios/reset', UsuarioController::class, 'resetPassword');
    $r->add('POST', '/api/usuarios/toggle', UsuarioController::class, 'toggleEstado');
    $r->add('POST', '/api/usuarios/actualizar', UsuarioController::class, 'update');
    $r->add('POST', '/api/usuarios', UsuarioController::class, 'create');
    $r->add('POST', '/api/usuarios/avatar', UsuarioController::class, 'avatar');
    $r->add('POST', '/api/usuarios/eliminar-avatar', UsuarioController::class, 'eliminarAvatar');
    $r->add('POST', '/api/usuarios/mi-perfil', UsuarioController::class, 'miPerfil');
    $r->add('POST', '/api/usuarios/cambiar-clave', UsuarioController::class, 'cambiarClave');
};
