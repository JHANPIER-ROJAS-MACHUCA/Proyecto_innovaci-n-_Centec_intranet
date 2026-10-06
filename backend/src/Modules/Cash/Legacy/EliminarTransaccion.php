<?php

namespace CrediSoporte\Modules\Cash\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Installment;
use CrediSoporte\Domain\Models\Transaction;

// Logica migrada verbatim desde app/api/eliminarTransaccion.php.
// El wrapper en app/api/eliminarTransaccion.php preserva URL, entradas y salida legacy.
class EliminarTransaccion
{
    public static function handle(): void
    {
        global $database;
$transactionId = $_POST['id'];

if (!isset($transactionId) || empty($transactionId)) {
    echo json_encode([
        'message' => 'La transacción no existe.',
        'success' => false
    ]);
    die();
}

$transaction = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
    ->join('tcaja_oficina', 'tcaja_usuario.idCO', 'tcaja_oficina.idCO')
    ->where('tcaja_usu_detal.idCAD', $transactionId)
    ->whereNull('tcaja_oficina.fin')
    ->whereNull('tcaja_oficina.finH')
    ->first();

if (!$transaction) {
    echo json_encode([
        'message' => 'La transacción no existe.',
        'success' => false
    ]);
    die();
}

$installmentIds = [];
$moraIds = [];

// extraemos las cuotas pagadas
if ($transaction->idCuota !== null && $transaction->idCuota !== '') {
    $installmentIds = array_filter(explode(',', $transaction->idCuota));
}

// extraemos las moras pagadas
if ($transaction->idMora !== null && $transaction->idMora !== '') {
    $moraIds = array_filter(explode(',', $transaction->idMora));
}

// unimos las ids de las cuotas
$installmentAndMoraIds = array_unique(array_merge($installmentIds, $moraIds));

$installments = Installment::whereIn('idPD', $installmentAndMoraIds)
    ->orderBy('idPD', 'desc')
    ->get();

$credit = Credit::find($installments[0]->idP);
$credit->estado = 4;

$montoCuota = floatval($transaction->cuota);
$montoMora = floatval($transaction->mora);

foreach ($installments as $installment) {
    $afectado = false;

    // retorno de capital
    if (in_array($installment->idPD, $installmentIds) && $montoCuota > 0) {
        if ($installment->montoPagado <= round($montoCuota, 1)) {
            $montoCuota -= $installment->montoPagado;

            $installment->montoPagado = 0;
            $installment->fechaPago = null;
            $installment->estado = null;
            $installment->idU = null;

            $installment->finished_date = null;
            $installment->finished_time = null;

            $afectado = true;
        } else if ($installment->montoPagado > round($montoCuota, 1)) {
            $restante = round($installment->montoPagado - $montoCuota, 1);

            $installment->montoPagado = $restante > 0 ? $restante : null;
            $installment->estado = $restante > 0 ? 2 : null;
            $installment->fechaPago = $restante > 0 ? $installment->fechaPago : null;

            $installment->finished_date = null;
            $installment->finished_time = null;

            $montoCuota = 0;
            $afectado = true;
        }
    }

    // retorno de mora
    if (in_array($installment->idPD, $moraIds) && round($montoMora, 1) > 0) {
        $afectado = true;

        $montoMora -= $installment->pagoMora;

        if ($montoMora > 0) {
            $installment->pagoMora = 0;
        }else{
            $installment->pagoMora = $montoMora * -1;
        }

        $installment->tfechaMora = null;
        $installment->estado_mora = null;
    }

    if ($afectado) {
        $installment->save();
    }
}

if (round($montoMora, 1) > 0) {
    $credit->otras_moras -= $montoMora;
    $montoMora = 0;
}

if ($transaction->discount > 0) {
    $credit->discount -= $transaction->discount;
}

$credit->save();

$transaction->estadodt = '1'; // anulado
$transaction->save();

$database->table('textorno')
    ->where('cod', $transactionId)
    ->update(['estado' => 1]);

echo json_encode([
    'montoCuota' => $montoCuota,
    'montoMora' => $montoMora,
    'message' => 'Se elimino una transaccion.',
    'success' => true
]);
    }
}
