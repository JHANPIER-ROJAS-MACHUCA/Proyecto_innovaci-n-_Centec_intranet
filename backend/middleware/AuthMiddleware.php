<?php
require_once __DIR__ . '/../utils/Response.php';

class AuthMiddleware
{
    // Identidad CENTECPC: tusuarios (idU, userU, idRol) + tdatosu (nombre).
    // Clave 'tipoU' se conserva por compatibilidad pero ahora vale idRol
    // (1 Super Admin, 2 Asesor, 3 Cliente, 4 Postulante, 5 admin_personal,
    //  6 Soporte Web, 7 Plataforma, 8 Gerente, 9 Seguimiento).
    public static function user(): ?array
    {
        if (empty($_COOKIE['user1'])) return null;
        global $capsule;
        $idU = (int) $_COOKIE['user1'];
        $row = $capsule->table('tusuarios as u')->leftJoin('tdatosu as d', 'd.idU', 'u.idU')
            ->where('u.idU', $idU)->where('u.idEstado', 1)
            ->select('u.idU', 'u.userU', 'u.idRol', 'd.nomU', 'd.apU', 'd.amU')->first();
        if (!$row) return null;
        return [
            'idU'   => $row->idU,
            'nombre'=> trim(($row->apU ?? '') . ' ' . ($row->amU ?? '') . ' ' . ($row->nomU ?? '')),
            'tipoU' => $row->idRol,
            'idO'   => $_COOKIE['tofi'] ?? null,
        ];
    }

    public static function requireAuth(): array
    {
        $u = self::user();
        if (!$u) Response::error('No autenticado.', 401);
        return $u;
    }
}
