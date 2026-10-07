<?php
require_once __DIR__ . '/../repositories/OperativaRepository.php';
require_once __DIR__ . '/../repositories/CajaRepository.php';

class BilletajeService
{    const DENOM = ['b200' => 200, 'b100' => 100, 'b50' => 50, 'b20' => 20, 'b10' => 10, 'm5' => 5, 'm2' => 2, 'm1' => 1, 'm05' => 0.5, 'm02' => 0.2, 'm01' => 0.1];

    public static function registrar(array $user, array $in): array
    {
        $hoy = date('Y-m-d');
        if (BilletajeRepository::existeHoy($user['idU'], $hoy)) {
            throw new DomainException('Billetaje de hoy ya registrado.');
        }
        $row = ['idU' => $user['idU'], 'fecha' => $hoy];
        $total = 0;
        foreach (self::DENOM as $d => $v) {
            $n = (int) ($in[$d] ?? 0);
            $row[$d] = $n;
            $total += $n * $v;
        }
        $row['total'] = round($total, 2);
        $id = BilletajeRepository::registrar($row);
        return ['id' => $id, 'total' => $row['total']];
    }

    public static function pendientes()
    {
        return BilletajeRepository::pendientes();
    }

    public static function confirmar(int $id): void
    {
        BilletajeRepository::confirmar($id);
    }
}

class ReciboService
{
    public static function motivos(?string $tipoM)
    {
        return ReciboRepository::motivos($tipoM);
    }

    public static function registrar(array $user, object $caja, array $in): int
    {
        $motivo = ReciboRepository::motivo((int) ($in['motivo'] ?? 0));
        if (!$motivo) throw new DomainException('Motivo inválido.');
        $monto = (float) ($in['monto'] ?? 0);
        if ($monto <= 0) throw new DomainException('Monto inválido.');
        return ReciboRepository::registrar([
            'idCA' => $caja->idCA, 'tipo' => $motivo->idam, 'cuota' => $monto, 'total' => $monto,
            'cliente' => $in['cliente'] ?? null, 'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

class ExtornoService
{
    public static function solicitar(array $user, array $in): void
    {
        $cod = $in['codigo'] ?? null;
        $motivo = trim($in['motivo'] ?? '');
        if (!$cod || $motivo === '') throw new DomainException('codigo y motivo requeridos.');
        if (!ExtornoRepository::transaccionValida($cod)) throw new DomainException('Transacción no válida para extorno.');
        if (ExtornoRepository::solicitado($cod)) throw new DomainException('Extorno ya solicitado.');
        ExtornoRepository::solicitar(['cod' => $cod, 'motivo' => $motivo, 'fecha' => date('Y-m-d'), 'idU' => $user['idU']]);
    }

    public static function listar()
    {
        return ExtornoRepository::listar();
    }

    public static function resolver($cod): void
    {
        if (!$cod) throw new DomainException('codigo requerido.');
        try {
            ExtornoRepository::resolver($cod);
        } catch (\Throwable $th) {
            throw new DomainException('No se pudo aplicar el extorno.');
        }
    }
}

class BovedaService
{
    public static function saldos()
    {
        return BovedaRepository::saldos();
    }

    public static function designar(int $idU, array $in): int
    {
        $monto = (float) ($in['monto'] ?? 0);
        if ($monto <= 0 || empty($in['idO'])) throw new DomainException('monto e idO requeridos.');
        return BovedaRepository::designar([
            'fecha' => date('Y-m-d'), 'monto' => $monto, 'idO' => $in['idO'],
            'idU' => $idU, 'tipo' => $in['tipo'] ?? '1', 'estado' => '2',
        ]);
    }

    public static function consumir(int $id): void
    {
        BovedaRepository::consumir($id);
    }
}
