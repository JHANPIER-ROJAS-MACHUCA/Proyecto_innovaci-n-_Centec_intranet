<?php
// Módulo Sucursales — servicio (movido de controllers/SucursalController.php,
// phase2-modular; lógica idéntica).
// Listado + detalle completo (empresa, métodos de pago, tipos
// crédito/ahorro/seguro, servicios, asesores). Origen CENTECPC:
// SucursalesController::index/detalle/listarHome + SucursalesModel::conMetodosPago.
require_once __DIR__ . '/../Repositories/SucursalRepository.php';

class SucursalService
{
    public static function listar(): array
    {
        $out = [];
        foreach (SucursalRepository::todas() as $s) {
            $asesores = array_values(array_filter(
                SucursalRepository::asesores((int) $s['idS']),
                fn($a) => (int) ($a['idEstado'] ?? 0) === 1
            ));
            $s['asesores'] = $asesores;
            SucursalRepository::conMetodosPago($s);
            $out[] = $s;
        }
        return $out;
    }

    public static function home(): array
    {
        $principal = SucursalRepository::principal();
        if ($principal) SucursalRepository::conMetodosPago($principal);
        return ['principal' => $principal, 'sucursales' => self::listar()];
    }

    public static function detalle(int $idS): array
    {
        $s = SucursalRepository::porId($idS);
        if (!$s) throw new DomainException('Sucursal no encontrada.');
        $asesores = SucursalRepository::asesores($idS);
        SucursalRepository::conMetodosPago($s);
        $s['tiposCredito'] = SucursalRepository::tiposCredito($idS);
        $s['tiposAhorro'] = SucursalRepository::tiposAhorro($idS);
        $s['tiposSeguro'] = SucursalRepository::tiposSeguro($idS);
        $s['servicios'] = SucursalRepository::servicios($idS);
        $s['asesores_activos'] = array_values(array_filter($asesores, fn($a) => (int) ($a['idEstado'] ?? 0) === 1));
        $s['asesores_desvinculados'] = array_values(array_filter($asesores, fn($a) => (int) ($a['idEstado'] ?? 0) !== 1));
        return $s;
    }
}
