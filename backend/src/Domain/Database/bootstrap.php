<?php

// Arranque historico de la app (reemplaza a app/database/database.php).
// Expone $database (Capsule Manager) en el ambito que lo incluye,
// igual que antes. Los scripts nuevos deben usar Bootstrap::boot().

use CrediSoporte\Core\Bootstrap;

require_once __DIR__ . '/../../../vendor/autoload.php';

Bootstrap::boot(Bootstrap::root());

$database = Bootstrap::capsule();
