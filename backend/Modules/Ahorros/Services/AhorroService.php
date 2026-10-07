<?php
// Módulo Ahorros — servicio (movido de services/, phase2-modular; idéntico).
// Punto de contacto con Caja: registrar() escribe la transacción espejo en
// tcaja_usu_detal (dirección Ahorros→Caja). Transaction NO se mueve (transversal).
require_once __DIR__ . '/../Repositories/AhorroRepository.php';

class AhorroService
{
    public static function saldoDisponible(int $customerId): array
    {
        $ahorro = AhorroRepository::cuenta($customerId);
        $saldo = $ahorro ? AhorroRepository::saldo($ahorro->idA) : 0.0;
        return [$ahorro, $saldo];
    }

    public static function registrar(int $customerId, string $tipo, int $motivo, float $monto, int $idU, object $caja): int
    {
        [$ahorro, $saldo] = self::saldoDisponible($customerId);
        $ahorroId = $ahorro ? $ahorro->idA : AhorroRepository::crearCuenta($customerId);
        $detaId = AhorroRepository::registrarDetalle([
            'idA' => $ahorroId, 'monto' => $monto, 'fecha' => date('Y-m-d H:i:s'),
            'tipo' => $tipo, 'moti' => $motivo, 'idU' => $idU, 'estad' => 1,
            'balance' => $tipo === '7' ? $saldo + $monto : $saldo - $monto,
        ]);
        AhorroRepository::registrarCaja([
            'idCA' => $caja->idCA, 'tipo' => $motivo, 'cuota' => $monto,
            'idCuota' => $detaId, 'total' => $monto, 'cliente' => $customerId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $detaId;
    }

    public static function detalle(int $customerId)
    {
        return AhorroRepository::detalle($customerId);
    }

    public static function anular(int $id): void
    {
        AhorroRepository::anular($id);
    }
}
