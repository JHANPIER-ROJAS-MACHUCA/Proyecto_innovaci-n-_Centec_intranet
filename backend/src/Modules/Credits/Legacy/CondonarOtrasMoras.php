<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/condonarOtrasMoras.php.
// El wrapper en app/api/condonarOtrasMoras.php preserva URL, entradas y salida legacy.
class CondonarOtrasMoras
{
    public static function handle(): void
    {$request = new Request();

$credit = Credit::with('installments')
    ->find($request->installmentId);

if (!$credit) {
    echo json_encode([
        'success' => false,
        'message' => 'El credito no existe.'
    ]);
    die();
}

$credit->otras_moras += $request->amount;

$cuotasPendientes = 0;
$morasPendientes = 0;

foreach ($credit->installments as $keyInstallment => $installment) {
    $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
    $debtMora = $installment->debtMora($credit->mora, $nextInstallment);

    $debtCuota = $installment->debtInstallment();

    if ($debtCuota > 0) {
        $cuotasPendientes++;
    }

    if ($debtMora > 0) {
        $morasPendientes++;
    }
}

$lastInstallment = $credit->installments[count($credit->installments) - 1];
$moraCredito = $credit->debtMora($lastInstallment);

if ($moraCredito > 0) {
    $morasPendientes++;
}

if ($cuotasPendientes === 0 && $morasPendientes === 0) {
    $credit->estado = 5;
}

$credit->save();

echo json_encode([
    'success' => true,
    'message' => $credit->estado == 4 ? 'Mora condonado con exito!!' : 'Credito finalizado',
    'credito' => $credit,
    'cuotas_pendientes' => $cuotasPendientes,
    'morasPendientes' => $morasPendientes
]);
    }
}
