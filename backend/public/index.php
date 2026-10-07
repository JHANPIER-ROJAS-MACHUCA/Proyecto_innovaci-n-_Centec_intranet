<?php
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

$register = require __DIR__ . '/../routes/api.php';
$router = new Router;
$register($router);
$router->dispatch(new AppRequest);
