<?php

namespace CrediSoporte\Modules\Reports\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Logica migrada desde app/exel/creditosFinalizados.php
class CreditosFinalizados
{
    public static function handle(): void
    {
$request = new Request();
$spreadsheet = new Spreadsheet();

$credits = Credit::join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->where('tprestamo.estado', 5)
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->groupBy('tprestamo.idP')
    ->when($request->startDate and $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.finished_at', $request->only(['startDate', 'endDate']));
    })
    ->select(
        'tprestamo.idP',
        'tprestamo.fechaDesembolso',
        'tprestamo.capital',
        'tprestamo.interest_rate',
        'tprestamo.number_installments',
        'tprestamo.penalty',
        'tprestamo.payment_period'
    )
    ->selectRaw('concat_ws(" ", tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
    ->selectRaw('count(tpresta_detalle.expiration_at < tpresta_detalle.payment_date) as cuotasAtrasadas')
    ->selectRaw('sum(tpresta_detalle.interest) as interest')
    ->selectRaw('sum(tpresta_detalle.capital_payment) as capital_payment')
    ->selectRaw('sum(tpresta_detalle.interest_payment) as interest_payment')
    ->selectRaw('sum(tpresta_detalle.delay_payment) as delay_payment')
    ->selectRaw('min(tpresta_detalle.expiration_at) as firstDateInstallment')
    ->selectRaw('max(tpresta_detalle.expiration_at) as lastDateInstallment')
    ->get();

$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'CLIENTE')
    ->setCellValue('B1', 'FECHA INICIO')
    ->setCellValue('C1', 'FECHA FIN')
    ->setCellValue('D1', 'FORMA')
    ->setCellValue('E1', 'N° CUOTAS')
    ->setCellValue('F1', 'PRESTAMO')
    ->setCellValue('G1', 'TASA')
    ->setCellValue('H1', 'INTERES')
    ->setCellValue('I1', 'ABONO CUOTA')
    ->setCellValue('J1', 'ABONO MORA')
    ->setCellValue('K1', 'FECHA CANCELACION')
    ->setCellValue('L1', 'CUOTAS RETRASADOS')
    ->setCellValue('M1', 'DIAS VENCIDOS');

$row = 2;
foreach ($credits as $keyCredit => $credit) {

    $diasVencidos = 0;

    if ($credit->lastDateInstallment < $credit->finished_at) {
        $ini = new DateTime($credit->lastDateInstallment);
        $fin = new DateTime($credit->finished_at);
        $diasVencidos = $ini->diff($fin)->days;
    }

    $sheet->setCellValue('A' . $row, $credit->customer);
    $sheet->setCellValue('B' . $row, $credit->firstDateInstallment);
    $sheet->setCellValue('C' . $row, $credit->lastDateInstallment);
    $sheet->setCellValue('D' . $row, $credit->paymentPeriodToString());
    $sheet->setCellValue('E' . $row, $credit->number_installments);
    $sheet->setCellValue('F' . $row, $credit->capital / 10);
    $sheet->setCellValue('G' . $row, $credit->interest_rate);
    $sheet->setCellValue('H' . $row, $credit->interest / 10);
    $sheet->setCellValue('I' . $row, $credit->capital_payment / 10);
    $sheet->setCellValue('J' . $row, $credit->delay_payment / 10);
    $sheet->setCellValue('K' . $row, $credit->finished_at);
    $sheet->setCellValue('L' . $row, $credit->cuotasAtrasadas);
    $sheet->setCellValue('M' . $row, $diasVencidos);

    $row = $row + 1;
}

$leters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
foreach ($leters as $leter) {
    $sheet->getColumnDimension($leter)->setAutoSize(true);
}

$fileName = 'creditos-finalizados_';
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
