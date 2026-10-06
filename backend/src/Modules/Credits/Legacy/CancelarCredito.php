<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Installment;
use CrediSoporte\Domain\Models\Transaction;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/cancelarCredito.php.
// El wrapper en app/api/cancelarCredito.php preserva URL, entradas y salida legacy.
class CancelarCredito
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

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

$credit = Credit::with('installments', 'condoneDates')
    ->find($request->get('creditId'));

$delay = new Delay;
$delay->setHolidays($database->table('holidays')->pluck('date'));
$delay->setPenalty($credit->mora);
$delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
$delay->payment_period = $credit->payment_period;

$installmentSummary = $credit->installmentSummary($delay);
$creditSummary = $credit->creditSummary($delay);

$cuotasPendiente = [];
$sumaCuotasPendiente = 0;
$idCuotasPendiente = [];

$morasPendiente = [];
$sumaMorasPendiente = 0;
$idMorasPendiente = [];

foreach ($credit->installments as $key => $installment) {
    $protoKey = array_search((string) $installment->idPD, array_column($installmentSummary, 'id'));
    $proto = $installmentSummary[$protoKey];

    if ($installment->estado != 1) {
        array_push($cuotasPendiente, [
            'id' => $installment->idPD,
            'amount' => $installment->amount()
        ]);
        array_push($idCuotasPendiente, $installment->idPD);
        $sumaCuotasPendiente += $proto->deuda_cuota;
    }

    if ($proto->atrasado && !$installment->estado_mora) {
        array_push($morasPendiente, [
            'id' => $installment->idPD,
            'amount' => $proto->deuda_mora
        ]);
        array_push($idMorasPendiente, $installment->idPD);
        $sumaMorasPendiente += $proto->deuda_mora;
    }
}

$discount = floatval($request->get('discount'));
$deudaTotal = $sumaCuotasPendiente + $sumaMorasPendiente + $creditSummary->deuda_otras_moras;
$totalAPagar = $deudaTotal - $discount;

// validamos que el monto a pagar no sea menor o igual a 0
if ($totalAPagar <= 0) {
    echo json_encode([
        'message' => 'El total no debe ser a cero.',
        'success' => false
    ]);
    die();
}

$fechaYHoraPago = date('Y-m-d H:i:s');
foreach ($cuotasPendiente as $installment) {
    Installment::where('idPD', $installment['id'])
        ->update([
            'montoPagado' => $installment['amount'],
            'fechaPago' => $fechaYHoraPago,
            'estado' => 1,
            'idU' => $_COOKIE['user1']
        ]);
}

foreach ($morasPendiente as $mora) {
    Installment::where('idPD', $mora['id'])
        ->update([
            'pagoMora' => $mora['amount'],
            'estado_mora' => 1
        ]);
}

if ($creditSummary->deuda_otras_moras > 0) {
    $credit->otras_moras += $creditSummary->deuda_otras_moras;
    $sumaMorasPendiente += $creditSummary->deuda_otras_moras;
}

$credit->discount = $discount;
$credit->estado = 5;
$credit->save();

$transaction = new Transaction();
$transaction->idCA = $cash->idCA;
$transaction->tipo = 3;
$transaction->total = $totalAPagar;
$transaction->discount = $discount;
$transaction->cuota = $sumaCuotasPendiente;
$transaction->idCuota = implode(',', $idCuotasPendiente);
$transaction->mora = $sumaMorasPendiente;
$transaction->idMora = implode(',', $idMorasPendiente);
$transaction->cliente = $credit->idCG;
$transaction->estadodt = 2;
$transaction->saldo = 0;
$transaction->cuotas_pendientes = 0;
$transaction->created_at = $fechaYHoraPago;
$transaction->save();

echo json_encode([
    'isFinished' => true,
    'operationNumber' => $transaction->idCAD,
    'message' => "Credito finalizado con exito!!",
    'success' => true
]);
    }
}
