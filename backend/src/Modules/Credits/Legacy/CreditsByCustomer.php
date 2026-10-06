<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/creditsByCustomer.php.
// El wrapper en app/api/creditsByCustomer.php preserva URL, entradas y salida legacy.
class CreditsByCustomer
{
    public static function handle(): void
    {$request = new Request();

$credits = Credit::with('installments')
    ->select(
        'tprestamo.idP',
        'tprestamo.n_credito',
        'tprestamo.montoAprovado',
        'tprestamo.mora',
        'tprestamo.tipoP',
        'tclie_general.cel',
    )
    ->selectRaw('concat_ws(" ",tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->where(function ($query) use ($request) {
        $search = (string) $request->search;
        $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like ?", ['%' . $search . '%'])
            ->orWhere('tclie_general.dni', 'like', $search . '%');
    })
    ->where('tprestamo.estado', 4)
    ->limit(20)
    ->get();

$creditos = collect();
foreach ($credits as $credit) {

    $cuotas = collect();
    $totalPendienteCuota = 0; // pendiente capital + interes
    $totalCuota = 0;
    $totalMora = 0;
    foreach ($credit->installments as $key => $installment) {
        $pendiente = $installment->debtInstallment();

        $nextInstallment = $credit->installments[$key + 1] ?? null;
        $mora = $installment->debtMora($credit->mora, $nextInstallment);

        $diasAtrasados = 0;
        if ($installment->estado === '1') {
            $ini = new DateTime($installment->fechaProg);
            $fin = new DateTime($installment->fechaPago);
            $diasAtrasados = $ini->diff($fin)->days;
        } else {
            $ini = new DateTime($installment->fechaProg);
            $fin = new DateTime(date('Y-m-d'));

            if ($installment->fechaProg > date('Y-m-d')) {
                $diasAtrasados = $ini->diff($fin)->days * -1;
            } else {
                $diasAtrasados = $ini->diff($fin)->days;
            }
        }

        $totalCuota += $pendiente;

        if ($installment->fechaProg <= date('Y-m-d')) {
            $totalPendienteCuota += $pendiente;
        }

        if ($pendiente > 0 || $mora > 0) {
            $totalMora += $mora;

            $cuotas->push([
                'id' => $installment->idPD,
                'number' => $installment->ncuota,
                'expiration_at' => $installment->fechaProg,
                'amount' => $installment->amount(),
                'slope' => $pendiente,
                'mora' => $mora,
                'days' => $diasAtrasados,
                'pago' => 0,
                'pagoMora' => 0
            ]);
        }
    }

    $lastInstallment = $credit->installments[count($credit->installments) - 1];
    $otras_moras = $credit->debtMora($lastInstallment);

    $ini = new DateTime($lastInstallment->fechaProg);
    $fin = new DateTime(date('Y-m-d'));
    $days = $ini->diff($fin)->days;

    $creditos->push([
        'id' => $credit->idP,
        'customer' => $credit->customer,
        'number' => $credit->n_cuota,
        'cell_phone' => $credit->cel,
        'installments' => $cuotas,
        'pendiente' => round($totalPendienteCuota, 1),
        'total_mora' => round($totalMora + $otras_moras, 1),
        'otras_moras' => round($otras_moras, 1),
        'mora_installments' => round($totalMora),
        'days' => $days,
        'total_installment' => round($totalCuota, 1),
        'product' => $credit->productTypeToString(),
        'capital' => $credit->montoAprovado
    ]);
}

echo json_encode($creditos);
    }
}
