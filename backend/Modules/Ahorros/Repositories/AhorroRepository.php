<?php
// Módulo Ahorros — repositorio (extraído de repositories/FinancieraRepository.php,
// phase2-modular; idéntico). Tablas: tahorro, tahorro_deta. Punto de contacto
// con Caja: registrarCaja() inserta en tcaja_usu_detal (dirección Ahorros→Caja);
// anular() también actualiza la transacción espejo. Transaction NO se mueve.

class AhorroRepository
{
    public static function cuenta(int $customerId): ?object
    {
        global $capsule;
        return $capsule->table('tahorro')->where('id', $customerId)->first();
    }

    public static function saldo(int $idA): float
    {
        global $capsule;
        $s = $capsule->table('tahorro_deta')
            ->selectRaw('sum(if(tipo=7,monto,0)) - sum(if(tipo=8,monto,0)) as saldo')
            ->where('idA', $idA)->where('estad', 1)->first();
        return (float) ($s->saldo ?? 0);
    }

    public static function crearCuenta(int $customerId): int
    {
        global $capsule;
        return $capsule->table('tahorro')->insertGetId(['tipoA' => 1, 'id' => $customerId, 'motivo' => 11, 'estado' => 1]);
    }

    public static function registrarDetalle(array $row): int
    {
        global $capsule;
        return $capsule->table('tahorro_deta')->insertGetId($row);
    }

    public static function registrarCaja(array $row): void
    {
        global $capsule;
        $capsule->table('tcaja_usu_detal')->insert($row);
    }

    public static function detalle(int $customerId)
    {
        global $capsule;
        return $capsule->table('tahorro_deta as d')
            ->join('tahorro as a', 'd.idA', 'a.idA')
            ->where('a.id', $customerId)->where('d.estad', 1)
            ->orderBy('d.idAd', 'desc')->limit(100)->get();
    }

    public static function anular(int $idAd): void
    {
        global $capsule;
        $conn = $capsule->getConnection();
        $conn->beginTransaction();
        try {
            $capsule->table('tahorro_deta')->where('idAd', $idAd)->update(['estad' => 0]);
            $capsule->table('tcaja_usu_detal')->where('idCuota', $idAd)->update(['estadodt' => '1']);
            $conn->commit();
        } catch (\Throwable $th) {
            $conn->rollBack();
            throw $th;
        }
    }

    public static function porFecha(string $desde, string $hasta)
    {
        global $capsule;
        return $capsule->table('tahorro_deta as d')
            ->join('tahorro as a', 'd.idA', 'a.idA')
            ->leftJoin('tclie_general as c', 'a.id', 'c.idCG')
            ->where('d.estad', 1)->whereBetween('d.fecha', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->select('d.*', 'c.dni', 'c.ap', 'c.nom')->orderBy('d.idAd', 'desc')->limit(500)->get();
    }
}
