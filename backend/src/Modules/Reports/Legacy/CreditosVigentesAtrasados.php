<?php

namespace CrediSoporte\Modules\Reports\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Logica migrada desde app/exel/creditosVigentesAtrasados.php
class CreditosVigentesAtrasados
{
    public static function handle(): void
    {
$request = new Request();
$spreadsheet = new Spreadsheet();

$currentDate = $request->get('fecha', date('Y-m-d'));

$credits = Credit::with('installments')
    ->join('tpresta_detalle', 'tprestamo.idP', 'tpresta_detalle.idP')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->where('tprestamo.estado', 4) // activos a cobros
    ->where('tprestamo.pago', '!=', 1) // diferente de diario
    ->where('tpresta_detalle.fechaProg', '<', $currentDate)
    ->where(function ($query) {
        $query->where('tpresta_detalle.estado', '=', 2)
            ->orWhereNull('tpresta_detalle.estado');
    })
    ->groupBy('tprestamo.idP')
    ->get();

$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'CLIENTE');
$sheet->setCellValue('B1', 'CELULAR');
$sheet->setCellValue('C1', 'PRESTAMO');
$sheet->setCellValue('D1', 'TASA');
$sheet->setCellValue('E1', 'CUOTA');
$sheet->setCellValue('F1', 'FORMA PAGO');
$sheet->setCellValue('G1', 'N° CUOTAS');
$sheet->setCellValue('H1', 'CUOTAS ATRASADAS');
$sheet->setCellValue('I1', 'PENDIENTE');
$sheet->setCellValue('J1', 'MORA');
$sheet->setCellValue('K1', 'TOTAL A PAGAR');

$row = 2;
foreach ($credits as $keyCredit => $credit) {
    $montoAtrasado = 0;
    $cuotasRetazadas = 0;
    $mora = 0;
    foreach ($credit->installments as $keyInstallment => $installment) {
        if ($installment->estado !== "1" && $installment->fechaProg < date('Y-m-d')) {
            $cuotasRetazadas++;
        }

        if ($installment->fechaProg <= date('Y-m-d')) {
            $montoAtrasado += $installment->debtInstallment();
        }

        $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
        $mora += $installment->debtMora($credit->mora, $nextInstallment);
    }

    $lastInstallment = $credit->installments[count($credit->installments) - 1] ?? null;
    $mora += $credit->debtMora($lastInstallment);

    $sheet->setCellValue('A' . $row, $credit->ap . ' ' . $credit->am . ' ' . $credit->nom);
    $sheet->setCellValue('B' . $row, $credit->cel);
    $sheet->setCellValue('C' . $row, round($credit->montoAprovado,1));
    $sheet->setCellValue('D' . $row, $credit->taza);
    $sheet->setCellValue('E' . $row, round($credit->cuota,1));
    $sheet->setCellValue('F' . $row, $credit->paymentTypeToString());
    $sheet->setCellValue('G' . $row, $cuotasRetazadas);
    $sheet->setCellValue('H' . $row, $credit->n_cuota);
    $sheet->setCellValue('I' . $row, round($montoAtrasado,1));
    $sheet->setCellValue('J' . $row, round($mora,1));
    $sheet->setCellValue('K' . $row, round($montoAtrasado + $mora,1));

    $row = $row + 1;
}

$leters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
foreach ($leters as $leter) {
    $sheet->getColumnDimension($leter)->setAutoSize(true);
}

$fileName = "atrasados_" . $currentDate . ".xlsx";
$writer = new Xlsx($spreadsheet);
// $writer->save('hello_world.xlsx');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
$writer->save('php://output');
    }
}
