<?php
// NOTA phase2: ReporteService migró a Modules/Reportes/Services/.
// Aquí queda TransaccionService (transversal: Cobro, Reportes, Clientes, Campo).
require_once __DIR__ . '/../repositories/FinancieraRepository.php';
require_once __DIR__ . '/../repositories/CatalogoRepository.php';

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
