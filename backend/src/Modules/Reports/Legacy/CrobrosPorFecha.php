<?php

namespace CrediSoporte\Modules\Reports\Legacy;

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Holiday;
use CrediSoporte\Domain\Models\User;
use CrediSoporte\Domain\Request\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Logica migrada desde app/exel/crobrosPorFecha.php
class CrobrosPorFecha
{
    public static function handle(): void
    {
$request = new Request();
$spreadsheet = new Spreadsheet();

$users = [];
if ($request->user()->tipoU === '1' || $request->user()->tipoU === '2' || $request->user()->tipoU === '3') {
    $users = User::active()->whereIn('tipoU', [3, 4])->get();
} else {
    $users = User::active()->where('idU', $request->user()->idU)->get();
}

$delay = new Delay;
$delay->setHolidays(Holiday::pluck('date'));

$currentDate = $request->get('fecha', date('Y-m-d'));

$credits = Credit::with('installments', 'condoneDates')
    ->join('tpresta_detalle', 'tpresta_detalle.idP', 'tprestamo.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->leftJoin('tusuario', 'tclie_general.idU', 'tusuario.idU')
    ->where('tprestamo.estado', 4)
    ->when($request->usuario, function ($query) use ($request) {
        $query->where('tclie_general.idU', $request->usuario);
    })
    ->when($request->lugar_cobro, function ($query) use ($request) {
        $query->where('tprestamo.tlocal', $request->lugar_cobro);
    })
    ->when($request->cliente, function ($query) use ($request) {
        $query->where(function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->whereRaw("concat_ws(' ',tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->cliente%'")
                    ->orWhere('tclie_general.dni', 'like', $request->cliente . '%');
            });
        });
    })
    ->when($request->user()->tipoU !== '1' && $request->user()->tipoU !== '2', function ($query) use ($users) {
        $query->whereIn('tclie_general.idU', $users->pluck('idU'));
    })
    ->where('tpresta_detalle.expiration_at', $currentDate)
    ->groupBy('tprestamo.idP')
    ->orderBy('tclie_general.ap')
    ->orderBy('tclie_general.am')
    ->select(
        'tprestamo.idP',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom',
        'tclie_general.cel',
        'tprestamo.capital',
        'tprestamo.interest_rate',
        'tprestamo.payment_period',
        'tprestamo.penalty'
    )
    ->selectRaw('concat_ws(" ", tusuario.apU) as user')
    ->get();

$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'CLIENTE');
$sheet->setCellValue('B1', 'CELULAR');
$sheet->setCellValue('C1', 'FUNCIONARIO');
$sheet->setCellValue('D1', 'PRESTAMO');
$sheet->setCellValue('E1', 'TASA');
$sheet->setCellValue('F1', 'CUOTA');
$sheet->setCellValue('G1', 'FORMA PAGO');
$sheet->setCellValue('H1', 'DIAS');
$sheet->setCellValue('I1', 'M. ATRASADO');
$sheet->setCellValue('J1', 'M. PAGADO');
$sheet->setCellValue('K1', 'M. FALTANTE');
$sheet->setCellValue('L1', 'MORA');
$sheet->setCellValue('M1', 'TOTAL');

$row = 2;
foreach ($credits as $keyCredit => $credit) {
    $delay->penalty = $credit->penalty;
    $delay->payment_period = $credit->payment_period;
    $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();

    $interest = 0;

    $sumCapitalPending = 0;
    $sumInterestPending = 0;

    $sumPenaltyDebt = 0;

    $montoFaltante = 0;
    $montoAtrasado = 0;
    // $cuotasRetazadas = 0;
    $mora = 0;
    $dias = 0;
    $primeraCuotaNoCancelada = null;
    $amount = 0;

    $nextInstallmentPayment = null;
    $installmentNow = null;

    foreach ($credit->installments as $keyInstallment => $installment) {
        $interest += $installment->interest;

        if ($installment->expiration_at === $currentDate && $installmentNow === null) {
            $installmentNow = $installment;
        }

        $capitalDebt = $installment->capitalDebt();
        $interestDebt = $installment->interestDebt();

        $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
        $penaltyDebt = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);

        if (($capitalDebt + $interestDebt) > 0 && $nextInstallmentPayment === null) {
            $nextInstallmentPayment = $installment;
        }

        $sumPenaltyDebt += $penaltyDebt;

        if ($installment->expiration_at <= date('Y-m-d')) {
            $sumCapitalPending += $capitalDebt;
            $sumInterestPending += $interestDebt;
        }
    }

    $ini = new DateTime($nextInstallmentPayment->expiration_at);
    $fin = new DateTime(date('Y-m-d'));
    $dias = $ini->diff($fin)->days;

    if ($nextInstallmentPayment->expiration_at < date('Y-m-d')) {
        $dias = $dias * -1;
    }

    $sheet->setCellValue("A$row", $credit->ap . ' ' . $credit->am . ' ' . $credit->nom);
    $sheet->setCellValue("B$row", $credit->cel);
    $sheet->setCellValue("C$row", $credit->user);
    $sheet->setCellValue("D$row", ($credit->capital) / 10);
    $sheet->setCellValue("E$row", round($credit->interest_rate, 2));
    $sheet->setCellValue("F$row", ($installmentNow->capital + $installmentNow->interest) / 10);
    $sheet->setCellValue("G$row", $credit->paymentPeriodToString());
    $sheet->setCellValue("H$row", $dias);
    $sheet->setCellValue("I$row", ($sumCapitalPending + $sumInterestPending - $installmentNow->capitalDebt() - $installmentNow->interestDebt()) / 10);
    $sheet->setCellValue("J$row", ($installmentNow->capital_payment + $installmentNow->interest_payment) / 10);
    $sheet->setCellValue("K$row", ($installmentNow->capitalDebt() + $installmentNow->interestDebt()) / 10);
    $sheet->setCellValue("L$row", $sumPenaltyDebt / 10);
    $sheet->setCellValue("M$row", ($sumCapitalPending + $sumInterestPending + $sumPenaltyDebt) / 10);

    $row = $row + 1;
}

$leters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
foreach ($leters as $leter) {
    $sheet->getColumnDimension($leter)->setAutoSize(true);
}


$fileName = "cobrar_" . $currentDate . ".xlsx";
$writer = new Xlsx($spreadsheet);
// $writer->save('hello_world.xlsx');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
$writer->save('php://output');
    }
}
