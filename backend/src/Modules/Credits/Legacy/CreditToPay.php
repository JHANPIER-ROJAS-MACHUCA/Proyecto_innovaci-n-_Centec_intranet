<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/apiMobile/creditToPay.php.
// El wrapper en app/apiMobile/creditToPay.php preserva URL, entradas y salida legacy.
class CreditToPay
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

$credit = Credit::with('installments', 'condoneDates')
    ->select(
        'tprestamo.*',
        'credit_types.name as credit_type',
        'tclie_general.cel'
    )
    ->selectRaw('concat_ws(" ",tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->join('credit_types', 'tprestamo.credit_type_id', 'credit_types.id')
    ->where('tprestamo.estado', 4)
    ->where('tprestamo.idP', $request->creditId)
    ->first();

if (!$credit) {
    echo json_encode([
        'message' => 'El credito no existe.',
        'success' => false
    ]);
    die();
}

$delay = new Delay;
$delay->setHolidays($database->table('holidays')->pluck('date'));
$delay->setPenalty($credit->penalty);
$delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
$delay->payment_period = $credit->payment_period;

$cuotas = collect();
$totalPendienteCuota = 0; // pendiente capital + interes
$totalCuota = 0; // total capital + interes
$totalMora = 0; // total mora
foreach ($credit->installments as $key => $installment) {
    $capitalDebt = $installment->capitalDebt();
    $interestDebt = $installment->interestDebt();

    $pendiente = $capitalDebt + $interestDebt;

    $nextInstallment = $credit->installments[$key + 1] ?? null;
    // $mora = $installment->debtMora($credit->mora, $nextInstallment);
    $mora = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);

    $diasAtrasados = 0;
    if (!is_null($installment->payment_date)) {
        $ini = new DateTime($installment->expiration_at);
        $fin = new DateTime($installment->payment_date);
        $diasAtrasados = $ini->diff($fin)->days;

        if ($installment->expiration_at < $installment->payment_date) {
            $diasAtrasados = $ini->diff($fin)->days;
        } else {
            $diasAtrasados = $ini->diff($fin)->days;
        }
    } else {
        $ini = new DateTime($installment->expiration_at);
        $fin = new DateTime(date('Y-m-d'));

        if ($installment->expiration_at > date('Y-m-d')) {
            $diasAtrasados = $ini->diff($fin)->days * -1;
        } else {
            $diasAtrasados = $ini->diff($fin)->days;
        }
    }

    $totalCuota += $pendiente;

    if ($installment->expiration_at <= date('Y-m-d')) {
        $totalPendienteCuota += $pendiente;
    }

    if ($pendiente > 0 || $mora > 0) {
        $totalMora += $mora;

        $cuotas->push([
            'id' => $installment->idPD,
            'number' => $installment->number,
            'expiration_at' => $installment->expiration_at,
            'amount' => ($installment->capital + $installment->interest) / 10,
            'slope' => $pendiente / 10,
            'mora' => $mora / 10,
            'days' => $diasAtrasados,
            'pago' => 0,
            'pagoMora' => 0
        ]);
    }
}

$lastInstallment = $credit->installments[count($credit->installments) - 1];

$ini = new DateTime($lastInstallment->expiration_at);
$fin = new DateTime(date('Y-m-d'));
$days = $ini->diff($fin)->days;

echo json_encode([
    'id' => $credit->idP,
    'customer' => $credit->customer,
    'number' => $credit->n_cuota,
    'cell_phone' => $credit->cel,
    'installments' => $cuotas,
    'pendiente' => $totalPendienteCuota / 10,
    'total_mora' => $totalMora / 10,
    'otras_moras' => 0,
    'mora_installments' => $totalMora / 10,
    'days' => $days,
    'total_installment' => $totalCuota / 10,
    'product' => $credit->credit_type,
    'capital' => $credit->capital / 10
]);
    }
}
