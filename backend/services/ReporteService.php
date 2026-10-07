<?php
require_once __DIR__ . '/../repositories/FinancieraRepository.php';
require_once __DIR__ . '/../repositories/CatalogoRepository.php';
require_once __DIR__ . '/../Modules/Creditos/Repositories/MoraRepository.php';
require_once __DIR__ . '/../Modules/Ahorros/Repositories/AhorroRepository.php';

class TransaccionService
{
    public static function eliminar(int $idCAD): void
    {
        TransaccionRepository::eliminar($idCAD);
    }

    public static function movimientos(int $idCA)
    {
        return TransaccionRepository::movimientos($idCA);
    }

    public static function operacionesCliente(int $idCG)
    {
        return TransaccionRepository::operacionesCliente($idCG);
    }
}

class ReporteService
{
    public static function conteosPanel(): array
    {
        $c = CpanelRepository::conteos();
        return ['clientes' => $c['clientes'], 'creditosActivos' => $c['creditosActivos'], 'creditosPropuestos' => $c['creditosPropuestos']];
    }

    public static function cobrosPorFecha(string $desde, string $hasta, ?int $idU = null)
    {
        return TransaccionRepository::porTipoFecha(3, $desde, $hasta, $idU);
    }

    public static function desembolsosPorFecha(string $desde, string $hasta, ?int $idU = null)
    {
        return TransaccionRepository::porTipoFecha(2, $desde, $hasta, $idU);
    }

    public static function cierre(string $mes)
    {
        return TransaccionRepository::cierre($mes);
    }

    public static function morasPorDias()
    {
        return MoraRepository::morasPorDias();
    }

    public static function sentinel()
    {
        return ReporteRepository::sentinel();
    }

    public static function cancelados()
    {
        return ReporteRepository::cancelados();
    }

    public static function sinCreditos()
    {
        return ReporteRepository::sinCreditos();
    }

    public static function vinculaciones(?string $titular)
    {
        return ReporteRepository::vinculaciones($titular);
    }

    public static function proyecciones(string $desde, string $hasta): array
    {
        return ReporteRepository::proyecciones($desde, $hasta);
    }

    public static function ahorrosPorFecha(string $desde, string $hasta)
    {
        return AhorroRepository::porFecha($desde, $hasta);
    }

    public static function eliminados(int $page)
    {
        return TransaccionRepository::eliminados(max(1, $page));
    }
}
