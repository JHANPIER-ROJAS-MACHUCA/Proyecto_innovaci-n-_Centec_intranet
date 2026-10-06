<?php

namespace CrediSoporte\Modules\Reports\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Logica migrada desde app/exel/creditosFinalizadosActivos.php
class CreditosFinalizadosActivos
{
    public static function handle(): void
    {
$request = new Request();
$spreadsheet = new Spreadsheet();

$credits = Credit::with('installments')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->leftJoin('tusuario', 'tprestamo.user_id', 'tusuario.idU')
    ->whereIn('tprestamo.estado', [4, 5])
    ->when($request->startDate && $request->endDate, function ($query) use ($request) {
        $query->whereBetween('tprestamo.fechaDesembolso', [$request->startDate, $request->endDate]);
    })
    ->when($request->employee, function ($query) use ($request) {
        $query->where('tprestamo.user_id', $request->employee);
    })
    ->when($request->customer, function ($query) use ($request) {
        $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
    })
    ->orderBy('tprestamo.idP', 'desc')
    ->select(
        'tprestamo.*',
        'tclie_general.dni',
        'tclie_general.ap',
        'tclie_general.am',
        'tclie_general.nom',
        'tusuario.apU',
        'tusuario.amU',
        'tusuario.nomU'
    )
    ->get();

$row = 1;
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue("A$row", 'CODIGO')
    ->setCellValue("B$row", 'DOCUMENT')
    ->setCellValue("C$row", 'CLIENTE')
    ->setCellValue("D$row", 'FECHA DESEMBOLSO')
    ->setCellValue("E$row", 'FECHA FINALIZACIÓN')
    ->setCellValue("F$row", 'PRESTAMO')
    ->setCellValue("G$row", 'TASA')
    ->setCellValue("H$row", 'INTERES')
    ->setCellValue("I$row", 'FORMA PAGO')
    ->setCellValue("J$row", 'N° CUOTAS')
    ->setCellValue("K$row", 'TOTAL PAGAR')
    ->setCellValue("L$row", 'ABONO CAPITAL')
    ->setCellValue("M$row", 'ABONO INTERES')
    ->setCellValue("N$row", 'SALTO TOTAL')
    ->setCellValue("O$row", 'SALTO CAPITAL')
    ->setCellValue("P$row", 'SALTO INTERES')
    ->setCellValue("Q$row", 'MORA ABONADO (minimo)')
    ->setCellValue("R$row", 'DEUDA MORA')
    ->setCellValue("S$row", 'FECHA CANCELADO')
    ->setCellValue("T$row", 'FUNCIONARIO');

foreach ($credits as $keyCredit => $credit) {
    $row++;

    $capital = 0;
    $interes = 0;
    $abonoCapital = 0;
    $abonoInteres = 0;
    $abonoMora = 0;
    $debtMora = 0;

    foreach ($credit->installments as $keyInstallment => $installment) {
        $capital += $installment->cuota;
        $interes += $installment->interest;

        if ($installment->estado === "1") {
            $abonoCapital += $installment->cuota;
            $abonoInteres += $installment->interest;
        } else if ($installment->estado === "2") {
            $abonoCapital += $installment->montoPagado;
        }

        if ($installment->estado_mora === "1") {
            $abonoMora += $installment->pagoMora;
        }

        $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;

        if ($credit->estado === "4") {
            $debtMora += $installment->debtMora($credit->mora, $nextInstallment);
        }
    }

    $lastInstallment = $credit->installments[count($credit->installments) - 1] ?? null;
    if ($credit->estado === "4") {
        $debtMora += $credit->debtMora($lastInstallment);
    }

    $sheet->setCellValue("A$row", $credit->idP);
    $sheet->setCellValue("B$row", "$credit->dni");
    $sheet->setCellValue("C$row", "$credit->ap $credit->am $credit->nom");
    $sheet->setCellValue("D$row", date('d/m/Y', strtotime($credit->fechaDesembolso)));
    $sheet->setCellValue("E$row", $lastInstallment->fechaProg ? date('d/m/Y', strtotime($lastInstallment->fechaProg)) : "");
    $sheet->setCellValue("F$row", round($credit->montoAprovado, 1));
    $sheet->setCellValue("G$row", $credit->taza);
    $sheet->setCellValue("H$row", round($interes, 1));
    $sheet->setCellValue("I$row", $credit->paymentTypeToString());
    $sheet->setCellValue("J$row", $credit->n_cuota);
    $sheet->setCellValue("K$row", round($capital + $interes, 1));
    $sheet->setCellValue("L$row", round($abonoCapital . 1));
    $sheet->setCellValue("M$row", round($abonoInteres, 1));
    $sheet->setCellValue("N$row", round($capital + $interes - $abonoCapital - $abonoInteres, 1));
    $sheet->setCellValue("O$row", round($capital - $abonoCapital, 1));
    $sheet->setCellValue("P$row", round($interes - $abonoInteres, 1));
    $sheet->setCellValue("Q$row", round($abonoMora, 1));
    $sheet->setCellValue("R$row", round($debtMora, 1));
    $sheet->setCellValue("S$row", $credit->estado === "5" ? date('d/m/Y', strtotime($lastInstallment->fechaPago)) : '');
    $sheet->setCellValue("T$row", "$credit->apU $credit->amU $credit->nomU");
}


// ajustamos las columas al contenido
$leters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
foreach ($leters as $leter) {
    $sheet->getColumnDimension($leter)->setAutoSize(true);
}

$date = $request->startDate && $request->endDate ? "-$request->startDate\_$request->endDate" : "";
$fileName = "DESEMBOLSOS$date.xlsx";
$writer = new Xlsx($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
$writer->save('php://output');
    }
}
