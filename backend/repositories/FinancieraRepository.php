<?php
class TransaccionRepository
{
    public static function eliminar(int $idCAD): void
    {
        \App\Models\Transaction::where('idCAD', $idCAD)->delete();
    }

    public static function porTipoFecha(int $tipo, string $desde, string $hasta, ?int $idU = null)
    {
        global $capsule;
        $q = $capsule->table('tcaja_usu_detal as t')
            ->leftJoin('tclie_general as c', 't.cliente', 'c.idCG')
            ->where('t.tipo', $tipo)->where('t.estadodt', 2)
            ->whereBetween('t.created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);
        if ($idU) $q->where('c.idU', $idU);
        return $q->select('t.*', 'c.dni', 'c.ap', 'c.nom')->orderBy('t.idCAD', 'desc')->limit(500)->get();
    }

    public static function cierre(string $mes)
    {
        global $capsule;
        return $capsule->table('tcaja_usu_detal')
            ->where('estadodt', 2)->where('created_at', 'like', $mes . '%')
            ->selectRaw('tipo, count(*) as n, sum(total) as total')->groupBy('tipo')->get();
    }

    public static function movimientos(int $idCA, int $limit = 200)
    {
        global $capsule;
        return $capsule->table('tcaja_usu_detal')->where('idCA', $idCA)
            ->orderBy('idCAD', 'desc')->limit($limit)->get();
    }

    public static function operacionesCliente(int $idCG, int $limit = 200)
    {
        global $capsule;
        return $capsule->table('tcaja_usu_detal')->where('cliente', $idCG)
            ->orderBy('idCAD', 'desc')->limit($limit)->get();
    }

    public static function eliminados(int $page, int $perPage = 20)
    {
        global $capsule;
        return $capsule->table('tcaja_usu_detal as t')
            ->join('tprestamo as p', 't.conejo', 'p.idP')
            ->join('tclie_general as c', 'p.idCG', 'c.idCG')
            ->join('tcaja_usuario as cu', 't.idCA', 'cu.idCA')
            ->join('tusuarios as u', 'cu.idU', 'u.idU')
            ->leftJoin('tdatosu as d', 'd.idU', 'u.idU')
            ->where('t.estadodt', '1')->where('t.tipo', '3')
            ->orderBy('t.idCAD', 'desc')->offset($perPage * ($page - 1))->limit($perPage)
            ->select('t.*', 'c.ap', 'c.am', 'c.nom', 'd.apU', 'd.amU', 'd.nomU')->get();
    }

    public static function persistirCobro(object $credit, object $caja, array $calc): array
    {
        global $capsule;
        $conn = $capsule->getConnection();
        $conn->beginTransaction();
        try {
            $pagoCapital = 0.0;
            $pagoInteres = 0.0;
            $pagoMora = 0.0;
            $cuotasId = [];
            $morasId = [];
            $nextPayment = null;
            $cuotasPendientes = $calc['countInstallment'];
            foreach ($calc['summaries'] as $s) {
                $pagoCapital = round($pagoCapital + $s['capitalPayment'], 1);
                $pagoInteres = round($pagoInteres + $s['interestPayment'], 1);
                $pagoMora = round($pagoMora + $s['penaltyPayment'], 1);
                if ($s['capitalPayment'] > 0.0 || $s['interestPayment'] > 0.0) $cuotasId[] = $s['id'];
                if ($s['penaltyPayment'] > 0.0) $morasId[] = $s['id'];
                if ($nextPayment === null && $s['payment_at'] === null) $nextPayment = $s['expiration_at'];
                if ($s['payment_at'] !== null) $cuotasPendientes--;
            }

            if (round($calc['debtCapital'] + $calc['debtInterest'], 1) === round($pagoCapital + $pagoInteres, 1)
                && (float) $calc['penalty'] === (float) $pagoMora) {
                $credit->estado = 5;
                $credit->save();
            }

            $transaction = \App\Models\Transaction::create([
                'cliente' => $credit->idCG,
                'idCA' => $caja->idCA,
                'tipo' => 3,
                'estadodt' => 2,
                'conejo' => $credit->idP,
                'created_at' => $calc['fechaYHoraPago'],
                'total' => $pagoCapital + $pagoInteres + $pagoMora,
                'cuota' => $pagoCapital + $pagoInteres,
                'idCuota' => implode(',', $cuotasId),
                'mora' => $pagoMora,
                'idMora' => implode(',', $morasId),
                'saldo' => round($calc['debtCapital'] + $calc['debtInterest'] - $pagoCapital - $pagoInteres, 1),
                'cuotas_pendientes' => $cuotasPendientes,
                'next_payment' => $nextPayment,
            ]);

            foreach ($calc['summaries'] as $s) {
                $capsule->table('tpresta_detalle')->where('idPD', $s['id'])->update([
                    'capital_payment' => $s['newCapitalPayment'],
                    'interest_payment' => $s['newInterestPayment'],
                    'delay_payment' => $s['newPenaltyPayment'],
                    'is_finished' => $s['isFinished'],
                    'fechaPago' => $s['updated_at'],
                    'payment_at' => $s['payment_at'],
                ]);
                $capsule->table('installment_payment')->insert([
                    'installment_id' => $s['id'],
                    'payment_id' => $transaction->idCAD,
                    'capital' => $s['capitalPayment'],
                    'interest' => $s['interestPayment'],
                    'penalty' => $s['penaltyPayment'],
                    'portfolio_id' => $credit->customer->idU ?? null,
                ]);
            }
            $conn->commit();
            return ['operationNumber' => $transaction->idCAD, 'message' => 'Crédito cobrado con éxito!!', 'success' => true];
        } catch (\Throwable $th) {
            $conn->rollBack();
            throw $th;
        }
    }
}

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

class MoraRepository
{
    public static function deudores(int $limit = 100)
    {
        global $capsule;
        return $capsule->table('tpresta_detalle as d')
            ->join('tprestamo as p', 'd.idP', 'p.idP')
            ->join('tclie_general as c', 'p.idCG', 'c.idCG')
            ->where('p.estado', 4)->where('d.estado', '!=', 1)->where('d.fechaProg', '<', date('Y-m-d'))
            ->select('c.idCG', 'c.dni', 'c.ap', 'c.am', 'c.nom', 'p.idP', 'd.fechaProg', 'd.cuota', 'd.montoPagado')
            ->orderBy('d.fechaProg')->limit($limit)->get();
    }

    public static function morasPorDias(int $limit = 200)
    {
        global $capsule;
        return $capsule->table('tpresta_detalle as d')
            ->join('tprestamo as p', 'd.idP', 'p.idP')
            ->join('tclie_general as c', 'p.idCG', 'c.idCG')
            ->where('p.estado', 4)->where('d.estado', '!=', 1)->where('d.fechaProg', '<', date('Y-m-d'))
            ->select('c.dni', 'c.ap', 'c.nom', 'p.idP', 'd.fechaProg')
            ->selectRaw('DATEDIFF(CURDATE(), d.fechaProg) as dias, round(d.cuota - d.montoPagado, 2) as deuda')
            ->orderBy('dias', 'desc')->limit($limit)->get();
    }

    public static function condonar(int $creditId, string $date): void
    {
        global $capsule;
        $capsule->table('condone_dates')->insert(['credit_id' => $creditId, 'date' => $date]);
    }

    public static function condonarTodas(int $creditId, array $dates): void
    {
        global $capsule;
        foreach ($dates as $d) {
            $capsule->table('condone_dates')->insert(['credit_id' => $creditId, 'date' => $d]);
        }
    }

    public static function limpiarHuerfanas(): int
    {
        global $capsule;
        return $capsule->table('tpresta_detalle')->whereNotNull('pagoMora')->whereNull('tfechaMora')->update(['pagoMora' => null]);
    }
}

class HolidayRepository
{
    public static function fechas()
    {
        global $capsule;
        return $capsule->table('holidays')->pluck('date');
    }
}
