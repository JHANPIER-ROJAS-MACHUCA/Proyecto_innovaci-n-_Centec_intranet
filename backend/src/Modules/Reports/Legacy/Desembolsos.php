<?php

namespace CrediSoporte\Modules\Reports\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Logica migrada desde app/exel/desembolsos.php
class Desembolsos
{
    public static function handle(): void
    {
$request = new Request();
$spreadsheet = new Spreadsheet();

$currentDate = $request->get('fecha', date('Y-m-d'));

$credits = Credit::join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->join('credit_types', 'tprestamo.credit_type_id', 'credit_types.id')
    ->leftJoin('tusuario', 'tprestamo.user_id', 'tusuario.idU')
    ->leftJoin('tusuario as portfolio', 'tclie_general.idU', 'portfolio.idU')
    ->whereIn('tprestamo.estado', [4, 5])
    ->when($request->startDate && $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.fechaDesembolso', $request->only(['startDate', 'endDate']));
    })
    ->when($request->employee, function ($query) use ($request) {
        if ($request->byPortfolio === 'on') {
            $query->where('tclie_general.idU', $request->employee);
        } else {
            $query->where('tprestamo.user_id', $request->employee);
        }
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->select(
        'tprestamo.*',
        'tclie_general.dni',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom',
        'credit_types.name'
    )
    ->selectRaw('concat_ws(" ", tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
    ->selectRaw('concat_ws(" ", tusuario.apU, tusuario.amU, tusuario.nomU) as user')
    ->selectRaw('concat_ws(" ", portfolio.apU, portfolio.amU, portfolio.nomU) as portfolio')
    ->orderBy('tprestamo.fechaDesembolso', 'desc')
    ->orderBy('tprestamo.idP')
    ->get();

$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'CODIGO')
    ->setCellValue('B1', 'CLIENTE')
    ->setCellValue('C1', 'PRESTAMO')
    ->setCellValue('D1', 'TASA')
    ->setCellValue('E1', 'FORMA PAGO')
    ->setCellValue('F1', 'PRODUCTO')
    ->setCellValue('G1', 'N° CUOTAS')
    ->setCellValue('H1', 'FECHA DESEMBOLSO')
    ->setCellValue('I1', 'USUARIO')
    ->setCellValue('J1', 'CARTERA')
    ->setCellValue('K1', 'ESTADO');

$row = 2;
foreach ($credits as $keyCredit => $credit) {

    $sheet->setCellValue('A' . $row, $credit->idP);
    $sheet->setCellValue('B' . $row, $credit->customer);
    $sheet->setCellValue('C' . $row, $credit->capital / 10);
    $sheet->setCellValue('D' . $row, $credit->interest_rate);
    $sheet->setCellValue('E' . $row, $credit->paymentPeriodToString());
    $sheet->setCellValue('F' . $row, $credit->name);
    $sheet->setCellValue('G' . $row, $credit->number_installments);
    $sheet->setCellValue('H' . $row, $credit->fechaDesembolso);
    $sheet->setCellValue('I' . $row, $credit->user);
    $sheet->setCellValue('J' . $row, $credit->portfolio);
    $sheet->setCellValue('K' . $row, $credit->estado === '4' ? 'DESEMBOLSADO' : 'FINALIZADO');

    $row = $row + 1;
}

// ajustamos las columas al contenido
$leters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'k'];
foreach ($leters as $leter) {
    $sheet->getColumnDimension($leter)->setAutoSize(true);
}

$fileName = 'desembolsos_';
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
