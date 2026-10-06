<?php

// Configuracion general de la aplicacion. Lee del .env con valores por defecto seguros.
return [
    'name' => $_ENV['APP_NAME'] ?? 'CREDISOPORTE',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Lima',
    'api_path' => $_ENV['API_PATH'] ?? 'app/api',
];
