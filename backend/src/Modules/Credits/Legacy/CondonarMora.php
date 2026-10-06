<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Installment;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/condonarMora.php.
// El wrapper en app/api/condonarMora.php preserva URL, entradas y salida legacy.
class CondonarMora
{
    public static function handle(): void
    {$request = new Request();

$installment = Installment::where('idPD', $request->installmentId)
    ->whereNull('estado_mora')
    ->first();

if (!$installment) {
    echo json_encode([
        'success' => false,
        'message' => 'La cuota no existe.'
    ]);
    die();
}

$installment->pagoMora = $request->amount;
$installment->tfechaMora = date('Y-m-d');
$installment->estado_mora = 2;
$installment->save();

// verificar si el credito no tiene deuda

$credit = Credit::with('installments')
    ->find($installment->idP);

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
    $credit->save();
}

echo json_encode([
    'success' => true,
    'message' => $credit->estado == 4 ? 'Mora condonado con exito!!' : 'Credito finalizado',
    'credito' => $credit,
    'cuotas_pendientes' => $cuotasPendientes
]);
    }
}
