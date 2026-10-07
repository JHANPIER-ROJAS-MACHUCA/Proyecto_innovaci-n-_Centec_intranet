<?php
// Módulo Caja — servicio (movido de services/, phase2-modular; idéntico).
require_once __DIR__ . '/../Repositories/CajaRepository.php';

class CajaService
{
    public static function habilitada(array $user): ?object
    {
        return CajaRepository::habilitada($user);
    }

    public static function exigir(array $user): object
    {
        $caja = self::habilitada($user);
        if (!$caja) Response::json(['message' => 'La caja no esta habilitada.', 'success' => false], 409);
        return $caja;
    }

    public static function resumen(object $caja): array
    {
        return CajaRepository::resumen($caja);
    }

    public static function abrirGerencia(int $idU): int
    {
        return CajaRepository::abrirGerencia($idU);
    }

    public static function abrirOficina(array $user): int
    {
        return CajaRepository::abrirOficina($user);
    }

    public static function cerrar(int $idU, $monto): void
    {
        if ($monto === null || $monto === '') throw new DomainException('monto requerido.');
        CajaRepository::cerrar($idU, $monto);
        // Spec #4: snapshot de avance del día al cerrar (mejor esfuerzo).
        // Hook Caja → Reportes (analítica; sin dependencia inversa).
        try {
            require_once __DIR__ . '/../../Reportes/Services/AvanceService.php';
            AvanceService::guardarSnapshot(date('Y-m-d'), $idU);
        } catch (\Throwable $e) {}
    }

    // Spec #20d/#29: con billetaje de hoy cargado y sin verificar, el usuario
    // queda bloqueado para cobros y cargas de recibos (incluye faltante/sobrante).
    public static function bloqueoBilletaje(int $idU): ?array
    {
        require_once __DIR__ . '/../Repositories/OperativaRepository.php';
        return BilletajeRepository::bloqueoHoy($idU);
    }

    public static function exigirSinBloqueo(array $user): object
    {
        $caja = self::exigir($user);
        if (self::bloqueoBilletaje((int) $user['idU'])) {
            Response::json(['message' => 'Usuario bloqueado: billetaje de hoy pendiente de verificación (faltante/sobrante).', 'success' => false], 409);
        }
        return $caja;
    }
}
