<?php

namespace CrediSoporte\Modules\Reports\Legacy;

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Holiday;
use CrediSoporte\Domain\Request\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Logica migrada desde app/exel/carterasVencidas.php
class CarterasVencidas
{
    public static function handle(): void
    {
$request = new Request();
$spreadsheet = new Spreadsheet();

$currentDate = $request->get('fecha', date('Y-m-d'));

$delay = new Delay();
$delay->setHolidays(Holiday::pluck('date'));

$credits = Credit::with('installments')
    ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->select(
        'tprestamo.idP',
        'tprestamo.capital',
        'tprestamo.payment_period',
        'tprestamo.interest_rate',
        'tprestamo.penalty'
    )
    ->selectRaw('concat_ws(" ", tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
    ->selectRaw('min(tpresta_detalle.expiration_at) as i')
    ->selectRaw('max(tpresta_detalle.expiration_at) as f')
    ->where('tprestamo.estado', 4)
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->where(function ($query) use ($request) {
            $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
        });
    })
    ->groupBy('tprestamo.idP')
    ->when($request->startDate and $request->endDate, function ($query) use ($request) {
        $query->havingRaw("max(tpresta_detalle.expiration_at) between '$request->startDate' and '$request->endDate'");
    }, function ($query) {
        $query->havingRaw('max(tpresta_detalle.expiration_at) < date(now())');
    })
    ->get();

$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'CLIENTE')
    ->setCellValue('B1', 'FECHA INICIO')
    ->setCellValue('C1', 'FECHA FINAL')
    ->setCellValue('D1', 'FORMA PAGO')
    ->setCellValue('E1', 'PRESTAMO')
    ->setCellValue('F1', 'INTERES')
    ->setCellValue('G1', 'TOTAL CUOTA')
    ->setCellValue('H1', 'ABONO CUOTA')
    ->setCellValue('I1', 'ABONO INTERES')
    ->setCellValue('J1', 'SALDO CUOTA')
    ->setCellValue('K1', 'SALDO MORA')
    ->setCellValue('L1', 'TOTAL DEUDA')
    ->setCellValue('M1', 'CUOTAS RETRASADAS')
    ->setCellValue('N1', 'DIAS VENCIDOS');

$row = 2;
foreach ($credits as $keyCredit => $credit) {
    $delay->penalty = $credit->penalty;
    $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();

    $diasVencidos = 0;
    $cuotasRetrazadas = 0;

    $interes = 0;

    $sumCapitalPayment = 0;
    $sumInterestPayment = 0;

    $sumCapitalDebt = 0;
    $sumInterestDebt = 0;
    $sumPenaltyDebt = 0;

    foreach ($credit->installments as $keyInstallment => $installment) {
        $interes += $installment->interest;

        // pago
        $sumCapitalPayment += $installment->capital_payment;
        $sumInterestPayment += $installment->interest_payment;

        // deuda
        $capitalDebt = $installment->capitalDebt();
        $interestDebt = $installment->interestDebt();

        $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
        $penaltyDebt = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);
        
        $sumCapitalDebt += $capitalDebt;
        $sumInterestDebt += $interestDebt;
        $sumPenaltyDebt += $penaltyDebt;

        if (($capitalDebt + $interestDebt) > 0) {
            $cuotasRetrazadas++;
        }
    }

    $lastInstallment = $credit->installments[count($credit->installments) - 1] ?? null;

    $diasVencidos = 0;
    if ($lastInstallment) {
        $ini = new DateTime($lastInstallment->fechaProg);
        $fin = new DateTime(date('Y-m-d'));
        $diff = $ini->diff($fin);

        $diasVencidos = $diff->days;
    }


    $sheet->setCellValue('A' . $row, $credit->customer);
    $sheet->setCellValue('B' . $row, $credit->i);
    $sheet->setCellValue('C' . $row, $credit->f);
    $sheet->setCellValue('D' . $row, $credit->paymentPeriodToString());
    $sheet->setCellValue('E' . $row, $credit->capital / 10);
    $sheet->setCellValue('F' . $row, $interes / 10);
    $sheet->setCellValue('G' . $row, ($credit->capital + $interes) / 10);
    $sheet->setCellValue('H' . $row, $sumCapitalPayment / 10);
    $sheet->setCellValue('I' . $row, $sumInterestPayment / 10);
    $sheet->setCellValue('J' . $row, ($sumCapitalDebt + $sumInterestDebt) / 10);
    $sheet->setCellValue('K' . $row, $sumPenaltyDebt / 10);
    $sheet->setCellValue('L' . $row, ($sumCapitalDebt + $sumInterestDebt + $sumPenaltyDebt) / 10); // saldo total
    $sheet->setCellValue('M' . $row, $cuotasRetrazadas); // saldo capital
    $sheet->setCellValue('N' . $row, $diasVencidos);

    $row = $row + 1;
}

$leters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
foreach ($leters as $leter) {
    $sheet->getColumnDimension($leter)->setAutoSize(true);
}

$fileName = 'carteras-vencidas_';
if ($request->startDate && $request->endDate) {
    $fileName .= $request->startDate . '_' . $request->endDate;
} else {
    $fileName .= date('Y-m-d');
}

$fileName .= ".xlsx";
$writer = new Xlsx($spreadsheet);
// $writer->save('hello_world.xlsx');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
$writer->save('php://output');
    }
}
