<?php

namespace CrediSoporte\Modules\Cash\Legacy;

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Transaction;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/apiMobile/cobroMobile.php.
// El wrapper en app/apiMobile/cobroMobile.php preserva URL, entradas y salida legacy.
class CobroMobile
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

// el cobro solo esta disponible en usuarios logeados
if (!$request->user()) {
    echo json_encode([
        'message' => 'Debes de iniciar sesión',
        'success' => false,
    ]);
    die();
}

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

// el cobro solo esta disponible si el usuario tiene habilitado la caja.
if (!$cash) {
    echo json_encode([
        'message' => 'La caja no esta habilitada.',
        'success' => false
    ]);
    exit();
}

$credit = Credit::with('installments', 'customer', 'condoneDates')
    ->where('idP', $request->creditId)
    ->first();

if (!$credit) {
    echo json_encode([
        'message' => 'No existe el crédito.',
        'success' => false
    ]);
    die();
}

if (floatval($request->pagoCuota) === 0 && floatval($request->pagoMora) === 0) {
    echo json_encode([
        'message' => 'El monto a pagar debe ser mayor a cero.',
        'success' => false
    ]);
}

$delay = new Delay;
$delay->setHolidays($database->table('holidays')->pluck('date'));
$delay->setPenalty($credit->mora);
$delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
$delay->payment_period = $credit->payment_period;

$avance = 0;

$fechaYHora = date('Y-m-d H:i:s');
$fechaMora = date('Y-m-d');

$transaction = new Transaction();
$transaction->cuota = 0;
$transaction->mora = 0;
$transaction->cuotas_pendientes = 0;

$_pago = floatval($request->pagoCuota);
$_mora = floatval($request->pagoMora);

$_valid_cuota = 0;
$_valid_mora = 0;

$_has_installments_with_moras = false;

$installmentsToPay = [];
foreach ($credit->installments as $key => $installment) {
    $_with_installment = false;
    $_with_mora = false;
    $cuota = $installment->debtInstallment();

    $nextInstallment = $credit->installments[$key + 1] ?? null;
    // $mora = $installment->debtMora($credit->mora, $nextInstallment);
    $mora = $delay->penaltyFromInstallment($installment, $nextInstallment?->fechaProg);

    $_valid_cuota += $cuota;
    $_valid_mora += $mora;

    if (round($_pago, 1) > 0 && $cuota > 0) {
        $_with_installment = true;

        if ($_pago >= $cuota) {
            if ($installment->fechaProg === date('Y-m-d')) {
                $avance = $cuota;
            }

            $installment->montoPagado = $installment->amount();
            $installment->estado = 1;
            $transaction->cuota += $cuota;
            $_pago -= (float) $cuota;
        } else {
            if ($installment->fechaProg === date('Y-m-d')) {
                $avance = $_pago;
            }

            $installment->montoPagado += $_pago;
            $installment->estado = 2;
            $transaction->cuota += $_pago;
            $transaction->cuotas_pendientes++;
            $transaction->next_payment = $installment->fechaProg;
            $_pago = 0;
        }

        $installment->fechaPago = $fechaYHora;
        $installment->idU = $_COOKIE['user1'];

        if (!$transaction->idCuota) {
            $transaction->idCuota = (string) $installment->idPD;
        } else {
            $transaction->idCuota .= ',' . $installment->idPD;
        }
    } else if (!$transaction->next_payment && $cuota > 0) {
        $transaction->next_payment = $installment->fechaProg;
    }

    if (!$_with_installment && $cuota > 0) {
        $transaction->cuotas_pendientes++;
    }

    if (round($_mora, 1) > 0 && $mora > 0) {
        $_with_mora = true;

        $_mora -= (float) $mora;

        $installment->estado_mora = 1;
        $installment->pagoMora = $mora;
        $installment->tfechaMora = $fechaMora;

        $transaction->mora += $mora;
        if (!$transaction->idMora) {
            $transaction->idMora = (string) $installment->idPD;
        } else {
            $transaction->idMora .= ',' . $installment->idPD;
        }
    }

    if ($mora > 0) {
        $_has_installments_with_moras = true;
    }

    if ($_with_installment || $_with_mora) {
        $installmentsToPay[] = $installment;
    }
}

$lastInstallment = $credit->installments[count($credit->installments) - 1];
// $otras_moras = $credit->debtMora($lastInstallment);
$otras_moras = $delay->penaltyFromCredit($credit, $lastInstallment->fechaProg);

$_valid_mora += $otras_moras;

if ($request->pagoCuota > $_valid_cuota) {
    echo json_encode([
        'message' => 'La cuota no debe ser mayor a ' . $_valid_cuota,
        'success' => false
    ]);
    die();
}

if ($request->pagoMora > $_valid_mora) {
    echo json_encode([
        'message' => 'La mora no debe ser mayor a ' . $_valid_mora,
        'success' => false
    ]);
    die();
}

if (round($_mora, 1) > 0) {
    $credit->otras_moras = floatval($credit->otras_moras) + $_mora;
    $transaction->mora += $_mora;
}

if (
    (float) $_valid_cuota === (float) $request->pagoCuota &&
    (float) $_valid_mora === (float) $request->pagoMora
) {
    $credit->estado = 5;
}

$transaction->idCA = $cash->idCA;
$transaction->tipo = 3;
$transaction->conejo = $credit->idP;
$transaction->cliente = $credit->idCG;
$transaction->saldo = $_valid_cuota - $request->pagoCuota;
$transaction->created_at = $fechaYHora;
$transaction->total = $transaction->cuota + $transaction->mora;
$transaction->estadodt = 2;

$isDigital = false;

$database::beginTransaction();

try {

    foreach ($installmentsToPay as $installment) {
        $installment->save();
    }

    $credit->save();

    $transaction->comentario = "MOBILE";
    $transaction->save();

    // registro de metodo de pago
    if ($request->payment_method_type == 'transferencia bancaria') {
        $isDigital = true;
        $database->table('transaction_details')->insert([
            'transaction_id' => $transaction->idCAD,
            'payment_method' => $request->payment_method_type,
            'reference' => $request->payment_method_reference,
            'date' => $request->payment_method_date,
            'account_id' => $request->payment_method_account
        ]);
    } else if ($request->payment_method_type == 'yape') {
        $isDigital = true;
        $database->table('transaction_details')->insert([
            'transaction_id' => $transaction->idCAD,
            'payment_method' => $request->payment_method_type,
            'account_id' => $request->payment_method_account
        ]);
    }

    //throw new Exception("Error Processing Request", 1);


    $database::commit();
} catch (\Throwable $th) {
    $database::rollback();
    // echo json_encode([
    //     'success' => false,
    //     'message' => $th->getMessage()
    // ]);
    echo json_encode([
        'message' => 'Lo sentimos se produjo un error desconocido.',
        'success' => false
    ]);
    die();
}

echo json_encode([
    'created_at' => date('d/m/Y', strtotime($transaction->created_at)),
    'operation' => $transaction->idCAD,
    'total' => $transaction->total,
    'voucher_path' => $_ENV['APP_URL'] . '/app/pdf/voucher.php?operacion=' . $transaction->idCAD,
    'cell_phone' => $credit->customer->cel,
    'isDigital' => $isDigital,
    'success' => true,
    'avance' => $avance
]);
die();

echo json_encode([
    'transaction' => $transaction,
    'request' => $request,
    'credit' => $credit,
    'valid_cuota' => $_valid_cuota,
    'valid_mora' => $_valid_mora,
    'pago' => $_pago,
    'mora' => $_mora
]);
    }
}
