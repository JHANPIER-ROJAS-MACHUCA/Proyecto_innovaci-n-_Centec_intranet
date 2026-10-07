<?php
// Página raíz de la API: evita el 404 al abrir index.php directo en el navegador.
class HealthController
{
    public static function index(AppRequest $req): void
    {
        Response::json([
            'api' => 'CENTECP Intranet',
            'endpoints' => [
                'GET /api/health',
                'POST /api/auth/login {usu,pas}',
                'GET /api/evaluaciones?tab=evaluados',
                'GET /api/documentos/evaluacion-pdf?id={grupo}',
                'GET /api/documentos/evaluacion-patrimonio?id={grupo}',
                'POST /api/clientes/registro-completo',
            ],
            'success' => true,
        ]);
    }

    public static function health(AppRequest $req): void
    {
        Response::json(['ok' => true, 'time' => date('Y-m-d H:i:s'), 'success' => true]);
    }
}
