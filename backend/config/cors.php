<?php

// CORS para el SPA (frontend en :3000 en desarrollo).
return [
    'allowed_origins' => ['http://localhost:3000'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization'],
];
