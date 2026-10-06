<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Transaction;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/cobrarCredito.php.
// El wrapper en app/api/cobrarCredito.php preserva URL, entradas y salida legacy.
class CobrarCredito
{
    public static function handle(): void
    {
        global $database;
define('INSTALLMENT_STATUS_COMPLETED', '1');
define('INSTALLMENT_STATUS_INCOMPLETED', '2');
define('CREDIT_STATUS_COMPLETED', '5');
define('CREDIT_STATUS_PENDING', '4');
define('TRANSACTION_TYPE_PAY_INSTALLMENT', '3');
define('TRANSACTION_STATUS_OK', '2');

$request = new Request();

$request->value = round(floatval($request->value), 1);
$request->mora = floatval($request->mora);

$cash = null;

// verificamos que tenga una caja habilitada
if ($_COOKIE['tuser'] == '2') {
    $cashOficina = $database::table('tcaja_oficina')
        ->select('idCO')
        ->where('idO', $_COOKIE['tofi'])
        ->orderBy('idCO', 'desc')
        ->first();

    $cash = $database::table('tcaja_usuario')
        ->where('idCO', $cashOficina->idCO)
        ->orderBy('idCA')
        ->first();
} else {
    $cash = $database::table('tcaja_usuario')
        ->where('idU', $_COOKIE['user1'])
        ->orderBy('idCA', 'desc')
        ->first();
}

if (!$cash) {
    echo json_encode([
        'message' => 'La caja no esta habilitada.',
        'success' => false
    ]);
    exit();
}
// -- fin verificacion caja --

$credit = Credit::with('installments', 'condoneDates', 'customer')
    ->where('idP', $request->creditId)
    ->where('estado', CREDIT_STATUS_PENDING)
    ->first();

// finalizamos si no existe el credito
if (!$credit) {
    echo json_encode([
        'message' => 'El credito no existe.',
        'success' => false
    ]);
    exit();
}

if (!$request->mora > 0 && !$request->value > 0) {
    echo json_encode([
        'message' => $request->paymentType === 'amount' ?
            'El monto a pagar debe ser mayor a 0.0 soles.' :
            'Debes de seleccionar al menos una cuota a pagar.',
        'success' => false
    ]);
    die();
}

$delay = new Delay;
$delay->setHolidays($database->table('holidays')->pluck('date'));
$delay->setPenalty($credit->mora);
$delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
$delay->payment_period = $credit->payment_period;


/**
 * INICIO
 */

$fechaYHoraPago = date('Y-m-d H:i:s');
$fechaPago = date('Y-m-d');
$horaPago = date('H:i:s');

$requestPayment = $request->payment_type === 'amount' ? floatval($request->value) : intval($request->value);
$requestPenalty = intval($request->mora);

$countPenalty = 0; // número de cuotas con mora
$countInstallment = 0; // número de cuotas pendiente (capital o interes)
$penalty = 0.0; // suma total de deuda mora
$sumDebtCapital = 0.0; // suma total de deuda capital
$sumDebtInterest = 0.0; // suma total de deuda interes

$_requestPayment = $requestPayment;
$_requestPenalty = $requestPenalty;

$summaries = [];
foreach ($credit->installments as $key => $installment) {
    if ($installment->is_finished) {
        continue;
    }

    $debtCapital = $installment->debtCapital();
    $debtInterest = $installment->debtInterest();

    $nextInstallment = $credit->installments[$key + 1] ?? null;
    $debtPenalty = $delay->penaltyFromInstallment($installment, $nextInstallment?->fechaProg);
    $penalty = round($debtPenalty + $penalty, 1);

    $sumDebtCapital = round($sumDebtCapital + $debtCapital, 1);
    $sumDebtInterest = round($sumDebtInterest + $debtInterest, 1);

    if ($debtCapital > 0.0 || $debtInterest > 0.0) {
        $countInstallment++;
    }

    if ($debtPenalty > 0.0) {
        $countPenalty++;
    }

    /* ------ */

    $pc = 0.0; // pago capital
    $pi = 0.0; // pago interes
    $pp = 0.0; // pago mora

    if ($request->paymentType === 'amount') {

        if (
            $debtCapital > 0.0 &&
            $_requestPayment > 0.0 &&
            $_requestPayment >= $debtCapital
        ) {
            $pc = $debtCapital;
            $_requestPayment = round($_requestPayment - $debtCapital, 1);
            $debtCapital = 0.0;
        } else if (
            $debtCapital > 0.0 &&
            $_requestPayment > 0.0
        ) {
            $pc = $_requestPayment;
            $_requestPayment = 0.0;
            $debtCapital = round($debtCapital - $pc, 1);
        }

        if (
            $debtInterest > 0.0 &&
            $_requestPayment > 0.0 &&
            $_requestPayment >= $debtInterest
        ) {
            $pi = $debtInterest;
            $_requestPayment = round($_requestPayment - $debtInterest, 1);
            $debtInterest = 0.0;
        } else if (
            $debtInterest > 0.0 &&
            $_requestPayment > 0.0
        ) {
            $pi = $_requestPayment;
            $_requestPayment = 0.0;
            $debtInterest = round($debtInterest - $pi, 1);
        }
    } else {

        if (
            $debtCapital > 0.0 &&
            $_requestPayment > 0
        ) {
            $pc = $debtCapital;
            $pi = $debtInterest;

            $_requestPayment--;
            $debtCapital = 0.0;
            $debtInterest = 0.0;
        }
    }

    if (
        $_requestPenalty > 0 &&
        $debtPenalty > 0.0
    ) {
        $pp = $debtPenalty;
        $_requestPenalty--;
        $debtPenalty = 0.0;
    }

    $payment_at = $installment->payment_at;
    if (
        $payment_at === null &&
        !$debtCapital > 0.0 &&
        !$debtInterest > 0.0 &&
        ($pc > 0.0 || $pi > 0.0)
    ) {
        $payment_at = $fechaPago;
    }

    if (
        $pc > 0.0 ||
        $pi > 0.0 ||
        $pp > 0.0
    ) {
        array_push($summaries, [
            'id' => $installment->idPD,
            'number' => $installment->ncuota,
            'capitalPayment' => $pc,
            'newCapitalPayment' => $installment->capital_payment + $pc,
            'interestPayment' => $pi,
            'newInterestPayment' => $installment->interest_payment + $pi,
            'penaltyPayment' => $pp,
            'newPenaltyPayment' => $installment->delay_payment + $pp,
            'isFinished' => !$debtCapital > 0.0 && !$debtInterest > 0.0 && !$debtPenalty > 0.0,
            'payment_at' => $payment_at,
            'updated_at' => $fechaYHoraPago,
            'expiration_at' => $installment->fechaProg
        ]);
    }
}

$lastInstallment = $credit->installments[count($credit->installments) - 1];
$penaltyCredit = $delay->penaltyFromCredit($credit, $lastInstallment->fechaProg);
$penalty = round($penaltyCredit + $penalty, 1);

if ($penaltyCredit > 0.0) {
    $countPenalty++;
}

$creditPentaltyPayment = 0.0;
if ($penaltyCredit > 0.0 && $_requestPenalty > 0) {
    $creditPentaltyPayment = $penaltyCredit;
    $_requestPenalty--;
}

if ($request->paymentType === 'amount' && $requestPayment > ($sumDebtCapital + $sumDebtInterest)) {
    echo json_encode([
        'message' => 'El monto a pagar no debe ser mayor a ' . ($sumDebtCapital + $sumDebtInterest),
        'success' => false
    ]);
    die();
} else if ($request->paymentType === 'installment' && $requestPayment > $countInstallment) {
    echo json_encode([
        'message' => 'El número de cuotas a pagar no debe ser mayor a ' . $countInstallment . '.',
        'success' => false
    ]);
    die();
}

if ($requestPenalty > $countPenalty) {
    echo json_encode([
        'message' => 'La mora a pagar no debe ser mayor a ' . $penalty . '.',
        'success' => false
    ]);
    die();
}

echo json_encode([
    'penalty' => $penalty,
    'debtCapital' => $sumDebtCapital,
    'debtInterest' => $sumDebtInterest,
    'resumen' => $summaries
]);
die();

$cuotasPendientes = $countInstallment;
$cuotasId = [];
$morasId = [];
$nextPayment = null;
$pagoCapital = 0.0;
$pagoInteres = 0.0;
$pagoMora = 0.0;
foreach ($summaries as $summary) {
    $pagoCapital = round($pagoCapital + $summary['capitalPayment'], 1);
    $pagoInteres = round($pagoInteres + $summary['interestPayment'], 1);
    $pagoMora = round($pagoMora + $summary['penaltyPayment'], 1);

    if ($summary['capitalPayment'] > 0.0 || $summary['interestPayment'] > 0.0) {
        array_push($cuotasId, $summary['id']);
    }

    if ($summary['penaltyPayment'] > 0.0) {
        array_push($morasId, $summary['id']);
    }

    if ($nextPayment === null && $summary['payment_at'] === null) {
        $nextPayment = $summary['expiration_at'];
    }

    if ($summary['payment_at'] !== null) {
        $cuotasPendientes--;
    }
}

if ($creditPentaltyPayment > 0.0) {
    $pagoMora = round($pagoMora + $creditPentaltyPayment);
}

$database::beginTransaction();

try {
    if (
        round($sumDebtCapital + $sumDebtInterest, 1) === round($pagoCapital + $pagoInteres, 1) &&
        (float) $penalty === (float) $pagoMora
    ) {
        $credit->estado = 5;
        $credit->save();
    }

    $transaction = Transaction::create([
        'cliente' => $credit->idCG,
        'idCA' => $cash->idCA,
        'tipo' => 3,
        'estadodt' => 2,
        'conejo' => $credit->idP,
        'created_at' => $fechaYHoraPago,
        'total' => $pagoCapital + $pagoInteres + $pagoMora,
        'cuota' => $pagoCapital + $pagoInteres,
        'idCuota' => implode(',', $cuotasId),
        'mora' => $pagoMora,
        'idMora' => implode(',', $morasId),
        'saldo' => round($sumDebtCapital + $sumDebtInterest - $pagoCapital - $pagoInteres, 1),
        'cuotas_pendientes' => $cuotasPendientes,
        'next_payment' => $nextPayment
    ]);

    foreach ($summaries as $key => $summary) {
        $database->table('tpresta_detalle')
            ->where('idPD', $summary['id'])
            ->update([
                'capital_payment' => $summary['newCapitalPayment'],
                'interest_payment' => $summary['newInterestPayment'],
                'delay_payment' => $summary['newPenaltyPayment'],
                'is_finished' => $summary['isFinished'],
                'fechaPago' => $summary['updated_at'],
                'payment_at' => $summary['payment_at']
            ]);

        $database->table('installment_payment')->insert([
            'installment_id' => $installment->idPD,
            'payment_id' => $transaction->idCAD,
            'capital' => $summary['capitalPayment'],
            'interest' => $summary['interestPayment'],
            'penalty' => $summary['penaltyPayment'],
            'portfolio_id' => $credit->customer->idU ?: null,
        ]);
    }

    $database::commit();

    echo json_encode([
        'operationNumber' => $transaction->idCAD,
        'message' => 'Crédito cobrado con éxito!!',
        'success' => true
    ]);
} catch (\Throwable $th) {
    $database::rollback();

    echo json_encode([
        'sd' => $th->getMessage(),
        'message' => 'Lo sentimos se produjo un error desconocido.',
        'success' => false
    ]);
}

die();
/**
 * FIN
 */

$fechaYHoraPago = date('Y-m-d H:i:s');
$fechaPago = date('Y-m-d');
$horaPago = date('H:i:s');

$transaction = new Transaction();
$transaction->cliente = $credit->idCG;
$transaction->idCA = $cash->idCA;
$transaction->tipo = 3; // cobro
$transaction->estadodt = 2; // ok
$transaction->cuotas_pendientes = 0;
$transaction->created_at = $fechaYHoraPago;

$_amount = $request->value; // saldo en cuotas o monto
$_mora = $request->mora; // saldo de mora

$_valid_mora = 0; // suma total de moras
$_valid_cuota = 0; // suma total de cuotas
$_valid_monto_cuota = 0; // suma monto total de cuotas

$_id_cuotas_pagadas = []; // ids de las cuotas pagadas
$_id_moras_pagadas = []; // ids de las cuotas que se pagaron moras

$cuotasPagadas = [];
foreach ($credit->installments as $key => $installment) {
    $_withInstallment = false;
    $_withMora = false;

    $_deuda = $installment->debtInstallment();

    // si la cuota no esta completa
    if ($_deuda > 0) {
        $_valid_cuota++;
        $_valid_monto_cuota += $_deuda;

        if ($_amount > 0) {
            $_withInstallment = true;
            array_push($_id_cuotas_pagadas, $installment->idPD);
        }

        if ($request->paymentType === 'installment' && $_amount > 0) {
            $installment->montoPagado = $installment->cuota + $installment->interest;
            $installment->estado = 1;
            $installment->idU = $_COOKIE['user1'];
            $installment->fechaPago = $fechaYHoraPago;

            $installment->finished_date = $fechaPago;
            $installment->finished_time = $horaPago;

            $transaction->cuota += $_deuda;
            $_amount--;
        } else if ($request->paymentType === 'amount' && $_amount > 0) {

            if ($_amount >= $_deuda) {
                $installment->montoPagado = $installment->cuota + $installment->interest;
                $installment->estado = 1;

                $installment->finished_date = $fechaPago;
                $installment->finished_time = $horaPago;

                $transaction->cuota += $_deuda;
                $_amount = round($_amount - $_deuda, 1);
            } else {
                $installment->montoPagado = $installment->montoPagado + $_amount;
                $installment->estado = 2;

                $transaction->cuota += $_amount;
                $_amount = 0;
                $transaction->cuotas_pendientes++;
                if (!$transaction->next_payment) {
                    $transaction->next_payment = $installment->fechaProg;
                }
            }

            $installment->idU = $_COOKIE['user1'];
            $installment->fechaPago = $fechaYHoraPago;
        } else {
            $transaction->cuotas_pendientes++;
            if (!$transaction->next_payment) {
                $transaction->next_payment = $installment->fechaProg;
            }
        }
    }

    $nextInstallment = $credit->installments[$key + 1] ?? null;
    // $mora = $installment->debtMora($credit->mora, $nextInstallment);
    $mora = $delay->penaltyFromInstallment($installment, $nextInstallment?->fechaProg);

    if ($mora > 0) {
        $_valid_mora++;
    }

    if ($mora > 0 && $_mora > 0) {
        $installment->pagoMora = $mora;
        $installment->tfechaMora = $fechaPago;
        // $installment->estado_mora = 1;

        $transaction->mora += $mora;
        array_push($_id_moras_pagadas, $installment->idPD);
        $_mora--;
        $_withMora = true;
    }

    // si la cuota esta vencido y no tiene un estado de mora
    // if ($installment->isAtrasado() && !$installment->estado_mora) {
    //     $nextInstallment = $credit->installments[$key + 1] ?? null;
    //     $mora = $installment->debtMora($credit->mora, $nextInstallment);

    //     if ($_mora > 0) {
    //         $installment->pagoMora = $credit->mora;
    //         $installment->tfechaMora = $fechaPago;
    //         $installment->estado_mora = 1;

    //         $transaction->mora += $credit->mora;
    //         array_push($_id_moras_pagadas, $installment->idPD);
    //         $_mora--;
    //         $_withMora = true;
    //         $_valid_mora++;
    //     }
    // }

    if ($_withInstallment || $_withMora) {
        array_push($cuotasPagadas, $installment);
    }
}

$lastInstallment = $credit->installments[count($credit->installments) - 1];
// $deudaOtrasMoras = $credit->debtMora($lastInstallment);
$deudaOtrasMoras = $delay->penaltyFromCredit($credit, $lastInstallment->fechaProg);


if ($deudaOtrasMoras > 0) {
    $_valid_mora++;

    if ($_mora > 0) {
        $transaction->mora += $deudaOtrasMoras;
        $credit->otras_moras += $deudaOtrasMoras;
    }
}

$transaction->conejo = $credit->idP;
$transaction->idCuota = implode(',', $_id_cuotas_pagadas);
$transaction->idMora = implode(',', $_id_moras_pagadas);
$transaction->total = $transaction->cuota + $transaction->mora;
$transaction->saldo = $_valid_monto_cuota - $transaction->cuota;

if ($transaction->total <= 0) {
    echo json_encode([
        'message' => 'El pago no puede ser 0',
        'success' => false
    ]);
    die();
}

if (
    ($request->paymentType === 'installment' && $request->value > $_valid_cuota) ||
    ($request->paymentType === 'amount' && $request->value > $_valid_monto_cuota)
) {
    echo json_encode([
        'message' => $request->paymentType
            ? 'El monto no debe ser mayor a ' . $_valid_monto_cuota
            : 'El número de cuotas no debe mayor a ' . $_valid_cuota,
        'success' => false
    ]);
    die();
}

// echo json_encode([
//     'valid mora' => $_valid_mora,
//     'mora' => $request->mora,
//     'cuotas pendiente' => $transaction,
//     'success' => false
// ]);
// die();

if (
    (int) $_valid_mora === (int) $request->mora
    && $transaction->cuotas_pendientes === 0
) {
    $credit->estado = 5;
}

$database::beginTransaction();

try {

    foreach ($cuotasPagadas as $installment) {
        $installment->save();
    }

    $credit->save();

    $transaction->save();

    // registro de metodo de pago
    if ($request->payment_method_type == 'transferencia bancaria') {
        $database->table('transaction_details')->insert([
            'transaction_id' => $transaction->idCAD,
            'payment_method' => $request->payment_method_type,
            'reference' => $request->payment_method_reference,
            'date' => $request->payment_method_date,
            'account_id' => $request->payment_method_account
        ]);
    } else if ($request->payment_method_type == 'yape') {
        $database->table('transaction_details')->insert([
            'transaction_id' => $transaction->idCAD,
            'payment_method' => $request->payment_method_type,
            'account_id' => $request->payment_method_account
        ]);
    }

    $database::commit();
} catch (\Throwable $th) {
    $database::rollback();
    echo json_encode([
        'message' => 'Lo sentimos se produjo un error desconocido.',
        'success' => false
    ]);
    die();
}

echo json_encode([
    'morasPendientes' => $transaction->moras_pendientes,
    'isFinished' => $credit->estado == 5,
    'operationNumber' => $transaction->idCAD,
    'message' => 'Crédito cobrado con éxito!!',
    'success' => true
]);
    }
}
