<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;

// Logica migrada verbatim desde app/api/detalleCredito.php.
// El wrapper en app/api/detalleCredito.php preserva URL, entradas y salida legacy.
class DetalleCredito
{
    public static function handle(): void
    {
        global $database;
define('CREDIT_STATUS_PENDING', 4);

$credit = Credit::with('installments', 'condoneDates')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->where('tprestamo.estado', CREDIT_STATUS_PENDING)
    ->select('tprestamo.*')
    ->selectRaw('concat_ws(" ", tclie_general.ap, tclie_general.nom, tclie_general.nom) as customer')
    ->find($_GET['creditId']);


// finalizamos si no existe el credito
if (is_null($credit)) {
    echo json_encode([
        'message' => 'El credito no existe.',
        'success' => false
    ]);
    exit();
}

$delay = new Delay;
$delay->setHolidays($database->table('holidays')->pluck('date'));
$delay->setPenalty($credit->mora);
$delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
$delay->payment_period = $credit->payment_period;

// $dataInstallments = [];
// foreach ($credit->installments as $key => $installment) {
//     $nextInstallment = $credit->installments[$key + 1] ?? null;
//     $penalty = $delay->penaltyFromInstallment($installment, $nextInstallment?->fechaProg);

//     array_push($dataInstallments, [
//         'id' => $installment->idPD,
//         'number' => $installment->ncuota,
//         'capital' => $installment->cuota,
//         'interest' => $installment->interest,
//         'debt_installment' => $installment->debtInstallment(),
//         'penalty' => $penalty,
//         'expiration_at' => $installment->fechaProg
//     ]);
// }

// $lastInstallment = $credit->installments[count($credit->installments) - 1];
// $penalty = $delay->penaltyFromCredit($credit, $lastInstallment->fechaProg);

// $dataCredit = [
//     'id' => $credit->idP,
//     'penaty' => $penalty,
//     'disbursement_date' => $credit->fechaDesembolso,
//     'expiration_at' => $lastInstallment->fechaProg
// ];

// echo json_encode([
//     'current_date' => date('Y-m-d'),
//     'installments' => $dataInstallments,
//     'credit' => $dataCredit
// ]);
// die();

$installmentSummary = $credit->installmentSummary($delay);
$creditSummary = $credit->creditSummary($delay);

// ----
$installmentTotalAPagarHoy = 0;
$next_payment = null;
$diasDeRetraso = 0;
$installmentsPending = [];
$installmentsAPagarHoy = [];
$morasPending = [];
$cuotasVencidas = 0;

$cuotasPasadas = 0;
$cuotasAdelantadas = 0;
$pagoCuotaHoy = false;

foreach ($credit->installments as $key => $installment) {
    $protoKey = array_search((string) $installment->idPD, array_column($installmentSummary, 'id'));
    $proto = $installmentSummary[$protoKey];

    if ($installment->estado !== "1") {
        array_push($installmentsPending, $proto->deuda_cuota);

        // cuotas que se tiene que pagar hasta hoy 
        if ($installment->fechaProg <= date('Y-m-d')) {
            array_push($installmentsAPagarHoy, $proto->deuda_cuota);
        }

        if ($next_payment === null) {
            $next_payment = $installment;
        }
    }

    $nextInstallment = $credit->installments[$key + 1] ?? null;
    // $mora = $installment->debtMora($credit->mora, $nextInstallment);
    $mora = $delay->penaltyFromInstallment($installment, $nextInstallment?->fechaProg);

    if ($mora > 0) {
        array_push($morasPending, $mora);
    }

    if ($proto->atrasado && $installment->estado !== "1") {
        $cuotasVencidas++;
    }

    if (
        $installment->estado === "1" &&
        $installment->fechaProg === date('Y-m-d') &&
        $installment->fechaPago === date('Y-m-d')
    ) {
        $pagoCuotaHoy = true;
    }
}

// mora despues que el credito finalizo
if ($creditSummary->deuda_otras_moras > 0) {
    array_push($morasPending, $creditSummary->deuda_otras_moras);
}

// dias de atraso del credito desde la primera cuota pendiente
if ($next_payment && $next_payment->fechaProg < date('Y-m-d')) {
    $ini = new DateTime($next_payment->fechaProg);
    $fin = new DateTime(date('Y-m-d'));
    $diff = $ini->diff($fin);

    $diasDeRetraso = $diff->days;
}

$estado = '';
if ($next_payment && $next_payment->fechaProg === date('Y-m-d')) {
    $estado = 'PENDIENTE';
} else if (count($installmentsAPagarHoy) > 1) {
    $estado = 'RETRASADO';
} else if (
    count($installmentsAPagarHoy) === 0 &&
    count($installmentsPending) > 0
) {
    $estado = 'ADELANTADO';
} else if (
    count($installmentsAPagarHoy) === 0 &&
    count($installmentsPending) === 0 &&
    count($morasPending) > 0
) {
    $estado = 'PENDIENTE MORA';
}

function suma($total, $item)
{
    $total += $item;
    return $total;
}

$_cuota_total = array_reduce($installmentsPending, "suma");
$_cuota_hoy = array_reduce($installmentsAPagarHoy, "suma");
$_mora_total = array_reduce($morasPending, "suma");

$interest_total = $credit->interestTotal();
$capital = (float) $credit->montoAprovado;
$interest_abonado = round($credit->installments->where('estado', 1)->sum('interest'), 1);
$capital_abonado = round($credit->installments->whereIn('estado', [1, 2])->sum('montoPagado') - $interest_abonado, 1);

echo json_encode([
    'id' => $credit->idP,
    'fechaDesembolso' => $credit->fechaDesembolso,
    'fechaFinalizacion' => $credit->installments[count($credit->installments) - 1]->fechaProg,
    'numeroCuotas' => $credit->n_cuota,
    'promedioPagoPorCuota' => $credit->cuota,
    'arrayCuotasPending' => $installmentsPending,
    'arrayMorasPending' => $morasPending,
    'cuotasAPagarHoy' => $installmentsAPagarHoy,
    'nextPayment' => $next_payment,
    'installmentTotal' => round($_cuota_total, 2),
    'installmentTotalAPagarHoy' => round($_cuota_hoy, 2),
    'moraTotalAPagar' => round($_mora_total, 2),
    'totalAPagar' => round($_cuota_hoy + $_mora_total, 2),
    'diasDeRetraso' => $diasDeRetraso,
    'cuotasVencidas' => $cuotasVencidas,
    'estado' => $estado,
    'customer_name' => $credit->customer,
    'capital' => (float) $credit->montoAprovado,
    'interest' => $interest_total,
    'interest_abonado' => $interest_abonado,
    'capital_abonado' => $capital_abonado,
    'interest_pendiente' => round($interest_total - $interest_abonado, 1),
    'capital_pendiente' => round($capital - $capital_abonado, 1),
    'justifyNonPayment' => $database->table('non_payment_justifications')
        ->where('credit_id', $credit->idP)
        ->whereDate('created_at', date('Y-m-d'))
        ->first()
]);
    }
}
