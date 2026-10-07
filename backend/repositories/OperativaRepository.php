<?php
class BovedaRepository
{
    public static function saldos()
    {
        global $capsule;
        return $capsule->table('tcaja_bodega')->orderBy('idBo', 'desc')->limit(100)->get();
    }

    public static function designar(array $row): int
    {
        global $capsule;
        return $capsule->table('tcaja_bodega')->insertGetId($row);
    }

    public static function consumir(int $id): void
    {
        global $capsule;
        $capsule->table('tcaja_bodega')->where('idBo', $id)->update(['estadoConsumo' => '2']);
    }

    // Spec #28: aceptar fondos asignados (solo su fila pendiente)
    public static function aceptar(int $id, int $idU): void
    {
        global $capsule;
        $n = $capsule->table('tcaja_bodega')->where('idBo', $id)->where('idU', $idU)->where('estadoConsumo', '1')->update(['estadoConsumo' => '2']);
        if (!$n) throw new DomainException('Asignación no disponible para aceptar.');
    }

    // Spec #28: eliminar asignación no aceptada (pendiente de aceptación)
    public static function eliminarPendiente(int $id): void
    {
        global $capsule;
        $n = $capsule->table('tcaja_bodega')->where('idBo', $id)->where('estadoConsumo', '1')->delete();
        if (!$n) throw new DomainException('Solo se puede eliminar una asignación pendiente.');
    }

    public static function pendientesDeAceptar(?int $idU = null)
    {
        global $capsule;
        $q = $capsule->table('tcaja_bodega')->where('estadoConsumo', '1')->orderByDesc('idBo')->limit(200);
        if ($idU) $q->where('idU', $idU);
        return $q->get();
    }
}

class BilletajeRepository
{
    public static function existeHoy($idU, string $fecha): bool
    {
        global $capsule;
        return (bool) $capsule->table('tbilletaje')->where('fecha', $fecha)->where('idU', $idU)->count();
    }

    // Spec #29: billetaje de hoy cargado (estado 2) y sin verificar (sin pasar a 1)
    public static function bloqueoHoy($idU): ?array
    {
        global $capsule;
        $r = $capsule->table('tbilletaje')->where('fecha', date('Y-m-d'))->where('idU', $idU)->where('estado', '2')->first();
        return $r ? (array) $r : null;
    }

    public static function registrar(array $row): int
    {
        global $capsule;
        return $capsule->table('tbilletaje')->insertGetId($row);
    }

    public static function pendientes()
    {
        global $capsule;
        return $capsule->table('tbilletaje as b')->join('tusuarios as u', 'b.idU', 'u.idU')
            ->leftJoin('tdatosu as d', 'd.idU', 'u.idU')
            ->where('b.estado', '2')->select('b.*', 'd.dniU')->orderBy('b.idBille', 'desc')->limit(100)->get();
    }

    public static function confirmar(int $id): void
    {
        global $capsule;
        $capsule->table('tbilletaje')->where('idBille', $id)->update(['estado' => '1']);
    }
}

class ReciboRepository
{
    public static function motivos(?string $tipoM)
    {
        global $capsule;
        $q = $capsule->table('tahorro_motivo')->where('estado', '1')->whereNull('monto')->whereNull('fecha')->orderBy('motivo');
        if ($tipoM) $q->where('tipoM', $tipoM);
        return $q->get();
    }

    public static function motivo(int $idam): ?object
    {
        global $capsule;
        return $capsule->table('tahorro_motivo')->where('idam', $idam)->where('estado', '1')->first();
    }

    public static function registrar(array $row): int
    {
        global $capsule;
        return $capsule->table('tcaja_usu_detal')->insertGetId($row);
    }
}

class ExtornoRepository
{
    public static function transaccionValida($cod): bool
    {
        global $capsule;
        return (bool) $capsule->table('tcaja_usu_detal')->where('idCAD', $cod)->where('estadodt', '2')->exists();
    }

    public static function solicitado($cod): bool
    {
        global $capsule;
        return (bool) $capsule->table('textorno')->where('cod', $cod)->exists();
    }

    public static function solicitar(array $row): void
    {
        global $capsule;
        $capsule->table('textorno')->insert($row);
    }

    public static function listar()
    {
        global $capsule;
        return $capsule->table('textorno as e')->join('tusuarios as u', 'e.idU', 'u.idU')
            ->leftJoin('tdatosu as d', 'd.idU', 'u.idU')
            ->select('e.*', 'd.dniU')->orderBy('e.fecha', 'desc')->limit(100)->get();
    }

    public static function resolver($cod): void
    {
        global $capsule;
        $conn = $capsule->getConnection();
        $conn->beginTransaction();
        try {
            $capsule->table('tcaja_usu_detal')->where('idCAD', $cod)->update(['estadodt' => '1']);
            $capsule->table('textorno')->where('cod', $cod)->delete();
            $conn->commit();
        } catch (\Throwable $th) {
            $conn->rollBack();
            throw $th;
        }
    }
}
