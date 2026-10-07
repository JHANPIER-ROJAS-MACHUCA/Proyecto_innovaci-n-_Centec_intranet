<?php
require_once __DIR__ . '/../services/PanelService.php';

class CpanelController
{
    public static function resumen(AppRequest $req): void
    {
        $user = AuthMiddleware::requireAuth();
        Response::json(['data' => CpanelService::resumen($user), 'success' => true]);
    }
}
