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

    // Spec #20b: solo cobros (tipo=3) de la caja, con cliente
    public static function cobrosDeCaja(int $idCA, int $limit = 200)
    {
        global $capsule;
        return $capsule->table('tcaja_usu_detal as t')
            ->leftJoin('tclie_general as c', 't.cliente', 'c.idCG')
            ->where('t.idCA', $idCA)->where('t.tipo', 3)
            ->select('t.*', 'c.dni', 'c.ap', 'c.nom')->orderBy('t.idCAD', 'desc')->limit($limit)->get();
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
            return ['operationNumber' => $transaction->idCAD, 'message' => 'CrÃ©dito cobrado con Ã©xito!!', 'success' => true];
        } catch (\Throwable $th) {
            $conn->rollBack();
            throw $th;
        }
    }
}


// NOTA phase2: AhorroRepository migró a Modules/Ahorros/Repositories/.


class HolidayRepository
{
    public static function fechas()
    {
        global $capsule;
        return $capsule->table('holidays')->pluck('date');
    }
}
