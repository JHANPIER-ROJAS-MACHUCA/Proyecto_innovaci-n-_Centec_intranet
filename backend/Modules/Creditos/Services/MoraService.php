<?php
// Módulo Creditos — moras y condonaciones (extraído de services/ReporteService.php,
// phase2-modular; idéntico).
require_once __DIR__ . '/../Repositories/MoraRepository.php';

class MoraService
{
    public static function deudores()
    {
        return MoraRepository::deudores();
    }

    public static function condonar(int $creditId, string $date): void
    {
        MoraRepository::condonar($creditId, $date);
    }

    public static function condonarTodas(int $creditId, array $dates): void
    {
        if (!$creditId || !count($dates)) throw new DomainException('creditId y dates[] requeridos.');
        MoraRepository::condonarTodas($creditId, $dates);
    }

    public static function limpiarHuerfanas(): int
    {
        return MoraRepository::limpiarHuerfanas();
    }
}
