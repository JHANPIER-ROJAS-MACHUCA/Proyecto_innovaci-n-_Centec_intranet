<?php

namespace CrediSoporte\Modules\Reports\Legacy;

use CrediSoporte\Domain\Helpers\Delay;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Holiday;
use CrediSoporte\Domain\Request\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Logica migrada desde app/exel/creditosActivos.php
class CreditosActivos
{
    public static function handle(): void
    {
$request = new Request();
$spreadsheet = new Spreadsheet();

$currentDate = $request->get('fecha', date('Y-m-d'));

$credits = Credit::with('installments', 'condoneDates')
    ->join('tclie_general', 'tprestamo.idCG', 'tclie_general.idCG')
    ->leftJoin('tusuario as user', 'user.idU', 'tprestamo.user_id')
    ->leftJoin('tusuario as cartera', 'tclie_general.idU', 'cartera.idU')
    ->where('tprestamo.estado', 4)
    ->where(function ($query) use ($request) {
        if ($request->startDate and $request->endDate) {
            $query->whereBetween('tprestamo.fechaDesembolso', [$request->startDate, $request->endDate]);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->employee) {
            $query->where('tprestamo.user_id', $request->employee);
        }
    })
    ->where(function ($query) use ($request) {
        if ($request->customer) {
            $query->whereRaw("concat_ws(' ', tclie_general.ap, tclie_general.am, tclie_general.nom) like '%$request->customer%'");
        }
    })
    ->select(
        'tprestamo.idP',
        'tprestamo.fechaDesembolso',
        'tprestamo.capital',
        'tprestamo.interest_rate',
        'tprestamo.number_installments',
        'tprestamo.penalty',
        'tprestamo.payment_period',
        'tprestamo.discount',
        'tclie_general.direc',
        'tclie_general.cel',
    )
    ->selectRaw('concat_ws(" ", tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
    ->selectRaw('concat_ws(" ", user.apU, user.amU, user.nomU) as user')
    ->selectRaw('concat_ws(" ", cartera.apU, cartera.amU, cartera.nomU) as cartera')
    ->get();

$delay = new Delay;
$delay->setHolidays(Holiday::pluck('date'));

$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'CODIGO')
    ->setCellValue('B1', 'CLIENTE')
    ->setCellValue('C1', 'CELULAR')
    ->setCellValue('D1', 'DIRECCION')
    ->setCellValue('E1', 'FECHA DESEMBOLSO')
    ->setCellValue('F1', 'FECHA FINALIZACION')
    ->setCellValue('G1', 'PRESTAMO')
    ->setCellValue('H1', 'TASA')
    ->setCellValue('I1', 'INTERES')
    ->setCellValue('J1', 'FORMA PAGO')
    ->setCellValue('K1', 'N° CUOTAS')
    ->setCellValue('L1', 'TOTAL A PAGAR')
    ->setCellValue('M1', 'ABONO CAPITAL')
    ->setCellValue('N1', 'ABONO INTERES')
    ->setCellValue('O1', 'SALDO')
    ->setCellValue('P1', 'SALDO CAPITAL')
    ->setCellValue('Q1', 'SALDO INTERES')
    ->setCellValue('R1', 'CUOTAS ATRASADAS')
    ->setCellValue('S1', 'MORA')
    ->setCellValue('T1', 'PENDIENTE A HOY')
    ->setCellValue('U1', 'SIGUIENTE PAGO')
    ->setCellValue('V1', 'DIAS ATRASADOS')
    ->setCellValue('W1', 'USUARIO')
    ->setCellValue('X1', 'CARTERA');

$row = 2;
foreach ($credits as $keyCredit => $credit) {
    $delay->setPenalty($credit->penalty);
    $delay->condone_dates = $credit->condoneDates->pluck('date')->toArray();
    $delay->payment_period = $credit->payment_period;

    $montoAtrasado = 0;
    $cuotasAtrasadas = 0;
    
    $numeroCuotas = count($credit->installments);
    $capital = 0;
    $interest = 0;
    
    $abonoCapital = 0;
    $abonoInterest = 0;
    
    // pendiente a hoy para nivelarse
    $pendingCapital = 0;
    $pendingInterest = 0;
    
    // total pendiente
    $sumCapitalDebt = 0;
    $sumInterestDebt = 0;
    $mora = 0;

    // siguiente cuota (obj)
    $nextDateInstallment = null;

    foreach ($credit->installments as $keyInstallment => $installment) {
        $capital += $installment->capital;
        $interest += $installment->interest;

        // una cuota completado ya abono capital y interes
        $abonoCapital += $installment->capital_payment;
        $abonoInterest += $installment->interest_payment;

        if (is_null($installment->payment_date) && $installment->expiration_at < date('Y-m-d')) {
            $cuotasAtrasadas++;
        }

        $capitalDebt = $installment->capitalDebt();
        $interestDebt = $installment->interestDebt();

        $sumCapitalDebt += $capitalDebt;
        $sumInterestDebt += $interestDebt;

        if ($installment->expiration_at <= date('Y-m-d')) {
            $pendingCapital += $capitalDebt;
            $pendingInterest += $interestDebt;
        }

        $nextInstallment = $credit->installments[$keyInstallment + 1] ?? null;
        $penalty = $delay->penaltyFromInstallment($installment, $nextInstallment?->expiration_at);
        $mora += $penalty;

        if (($capitalDebt + $interestDebt + $penalty) > 0 && $nextDateInstallment === null) {
            $nextDateInstallment = $installment->expiration_at;
        }
    }

    $lastInstallment = $credit->installments[$numeroCuotas - 1];

    $dias = 0;
    if ($nextDateInstallment !== null && $nextDateInstallment < date('Y-m-d')){
        $ini = new DateTime($nextDateInstallment);
        $fin = new DateTime(date('Y-m-d'));
        $dias = $ini->diff($fin)->days;
    }

    $sheet->setCellValue("A$row", $credit->idP);
    $sheet->setCellValue("B$row", $credit->customer);
    $sheet->setCellValue("C$row", $credit->cel);
    $sheet->setCellValue("D$row", $credit->direc);
    $sheet->setCellValue("E$row", $credit->fechaDesembolso);
    $sheet->setCellValue("F$row", $lastInstallment->expiration_at);
    $sheet->setCellValue("G$row", round($credit->capital / 10, 1));
    $sheet->setCellValue("H$row", $credit->interest_rate);
    $sheet->setCellValue("I$row", $interest / 10);
    $sheet->setCellValue("J$row", $credit->paymentPeriodToString());
    $sheet->setCellValue("K$row", $credit->number_installments);
    $sheet->setCellValue("L$row", ($capital + $interest) / 10);
    $sheet->setCellValue("M$row", $abonoCapital / 10);
    $sheet->setCellValue("N$row", $abonoInterest / 10);
    $sheet->setCellValue("O$row", ($sumCapitalDebt + $sumInterestDebt) / 10); // saldo total
    $sheet->setCellValue("P$row", $sumCapitalDebt / 10); // saldo capital
    $sheet->setCellValue("Q$row", $sumInterestDebt / 10); //saldo mora
    $sheet->setCellValue("R$row", $cuotasAtrasadas);
    $sheet->setCellValue("S$row", $mora / 10);
    $sheet->setCellValue("T$row", ($pendingCapital + $pendingInterest) / 10);
    $sheet->setCellValue("U$row", $nextDateInstallment);
    $sheet->setCellValue("V$row", $dias);
    $sheet->setCellValue("W$row", $credit->user);
    $sheet->setCellValue("X$row", $credit->cartera);

    $row = $row + 1;
}

$leters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X'];
foreach ($leters as $leter) {
    $sheet->getColumnDimension($leter)->setAutoSize(true);
}

$fileName = "creditos-activos_" . $currentDate . ".xlsx";
$writer = new Xlsx($spreadsheet);
// $writer->save('hello_world.xlsx');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
$writer->save('php://output');
    }
}
