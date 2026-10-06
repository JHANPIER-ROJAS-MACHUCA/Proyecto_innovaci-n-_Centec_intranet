<?php

// Rutas del modulo Auth. $router es CrediSoporte\Core\Router.

use CrediSoporte\Core\Response;
use CrediSoporte\Modules\Auth\AuthService;

$router->post('/auth/login', function () {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $dni = trim($input['dni'] ?? ($_POST['usu'] ?? ''));
    $pass = $input['password'] ?? ($_POST['pas'] ?? '');

    if ($dni === '' || $pass === '') {
        Response::error('DNI y contraseña son obligatorios.', 422);
        return;
    }

    $user = (new AuthService())->attempt($dni, $pass);

    if (!$user) {
        Response::error('Credenciales inválidas.', 401);
        return;
    }

    Response::json($user->makeVisible([])->toArray(), 'Bienvenido.');
});
