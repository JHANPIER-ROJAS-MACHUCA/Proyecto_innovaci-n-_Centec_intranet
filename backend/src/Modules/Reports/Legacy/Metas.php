<?php

namespace CrediSoporte\Modules\Reports\Legacy;

use CrediSoporte\Domain\Helpers\Holiday;
use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Goal;
use CrediSoporte\Domain\Request\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Logica migrada desde app/exel/metas.php
class Metas
{
    public static function handle(): void
    {
$request = new Request();
$spreadsheet = new Spreadsheet();

$days = [
    'Domingo',
    'Lunes',
    'Martes',
    'Miercoles',
    'Jueves',
    'Viernes',
    'Sabado'
];

$goal = Goal::join('tusuario', 'goals.user_id', 'tusuario.idU')->find($request->goalId);

if (!$goal) {
    echo 'No existe la meta';
    die();
}

$avances = Credit::select('fechaDesembolso as fecha')
    ->selectRaw('sum(montoAprovado) as desembolso')
    ->selectRaw('count(idP) as operaciones')
    ->whereIn('estado', [4, 5])
    ->where('user_id', $goal->user_id)
    ->whereBetween('fechaDesembolso', [$goal->start_at, $goal->end_at])
    ->groupBy('fechaDesembolso')
    ->get();

$oneDay = new DateInterval('P1D');
$start = new DateTime($goal->start_at);
$end = new DateTime($goal->end_at);

$diffDays = $start->diff($end)->days;
$fechasLaborables = [];

for ($i = 0; $i < $diffDays; $i++) {
    $fecha = $start->format('Y-m-d');

    if (Holiday::isHoliday($fecha)) {
        $start->add($oneDay);
        continue;
    }

    $fechasLaborables[] = [
        'fecha' => $fecha,
        'day' => $days[$start->format('w')]
    ];

    $start->add($oneDay);
}

$cellDiasLaborables = 'O9';
$cellMetaSaldo = 'O10';
$cellMetaOperaciones = 'O11';
$row = 1;

$totalDiasLaborables = count($fechasLaborables);
$saldo = $goal->saldo;

$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue("A$row", 'ITEM')
    ->setCellValue("B$row", 'DIAS LABORABLES')
    ->setCellValue("C$row", 'META DIARIA')
    ->setCellValue("D$row", 'EJECUTADO AL DIA')
    ->setCellValue("E$row", 'AVANCE')
    ->setCellValue("F$row", 'META MENSUAL')
    ->setCellValue("G$row", 'CUMPLIMIENTO')
    ->setCellValue("H$row", 'META DIARIA')
    ->setCellValue("I$row", 'EJECUTADO AL DIA')
    ->setCellValue("J$row", 'AVANCE')
    ->setCellValue("K$row", 'META MENSUAL')
    ->setCellValue("L$row", 'CUMPLIMIENTO');

foreach ($fechasLaborables as $key => $fechaLaborable) {
    $row++;

    $avance = $avances->where('fecha', $fechaLaborable['fecha'])->first();
    $desembolso = $avance->desembolso ?? 0;
    $operaciones = $avance->operaciones ?? 0;

    $sheet->setCellValue('A' . $row, $key + 1); // ITEM
    $sheet->setCellValue('B' . $row, "{$fechaLaborable['fecha']} {$fechaLaborable['day']}"); // FECHA

    $t = $totalDiasLaborables - $key;
    if ($key === 0) {
        $sheet->setCellValue("C$row", "=ROUND($cellMetaSaldo/$t,2)"); // META DIARIA SALDO
        $sheet->setCellValue("F$row", "=ROUND(D$row/$cellMetaSaldo*100,2)"); // PORCENTAJE META MENSUAL

        $sheet->setCellValue("H$row", "=ROUND($cellMetaOperaciones/$t,2)"); // META DIARIA OPERACIONES
        $sheet->setCellValue("K$row", "=ROUND(I$row/$cellMetaOperaciones*100,2)"); // PORCENTAJE META MENSUAL
    } else {
        $rsd = $row - 1; // columna adyacente
        $sheet->setCellValue("C$row", "=ROUND(($cellMetaSaldo-SUM(D1:D$rsd))/$t,2)"); // META DIARIA SALDO
        $sheet->setCellValue("F$row", "=ROUND(SUM(D1:D$row)/$cellMetaSaldo*100,2)"); // META MENSUAL

        $sheet->setCellValue("H$row", "=ROUND(($cellMetaOperaciones-SUM(I1:I$rsd))/$t,2)"); // META DIARIA OPERACIONES
        $sheet->setCellValue("K$row", "=ROUND(SUM(I1:I$row)/$cellMetaOperaciones*100,2)"); // META MENSUAL
    }

    $sheet->setCellValue("D$row", $desembolso);
    $sheet->setCellValue("E$row", "=ROUND(D$row/C$row*100,2)");

    $sheet->setCellValue("G$row", "=IF(OR(E$row>=100,E$row<0),\"SI\",\"NO\")");

    $sheet->setCellValue("I$row", $operaciones);
    $sheet->setCellValue("J$row", "=ROUND(I$row/H$row*100,2)");

    $sheet->setCellValue("L$row", "=IF(I$row>=ROUND(H$row,0),\"SI\",\"NO\")");
}


$sheet->setCellValue('N6', 'FUNCIONARIO')
    ->setCellValue('N7', 'FECHA INICIO')
    ->setCellValue('N8', 'FECHA FINAL')
    ->setCellValue('N9', 'DIAS LABORABLES')
    ->setCellValue('N10', 'META SALDO')
    ->setCellValue('N11', 'META OPERACIONES')
    ->setCellValue('N12', 'META SALDO')
    ->setCellValue('N13', 'META OPERACIONES')
    ->setCellValue('O6', "$goal->apU $goal->amU $goal->nomU")
    ->setCellValue('O7', $goal->start_at)
    ->setCellValue('O8', $goal->end_at)
    ->setCellValue('O9', $totalDiasLaborables)
    ->setCellValue('O10', $goal->saldo)
    ->setCellValue('O11', $goal->operation)
    ->setCellValue('O12', "=F$row")
    ->setCellValue('O13', "=K$row");

$sheet->mergeCells('O6:Q6')
    ->mergeCells('O7:P7')
    ->mergeCells('O8:P8');

// ajustamos las columas al contenido
$leters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'N'];
foreach ($leters as $leter) {
    $sheet->getColumnDimension($leter)->setAutoSize(true);
}

$rowStart = 2;
$sheet->getStyle("C$rowStart:D$row")
    ->getNumberFormat()
    ->setFormatCode('#,##0.00');
$sheet->getStyle("E$rowStart:F$row")
    ->getNumberFormat()
    ->setFormatCode('#0.00');

$sheet->getStyle("H$rowStart:H$row")
    ->getNumberFormat()
    ->setFormatCode("#0.00");
$sheet->getStyle("J$rowStart:K$row")
    ->getNumberFormat()
    ->setFormatCode('#0.00');

$conditional1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
$conditional1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
$conditional1->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL);
$conditional1->addCondition('"SI"');
$conditional1->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
$conditional1->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$conditional1->getStyle()->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN);
$conditional1->getStyle()->getFill()->getEndColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN);

$conditional2 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
$conditional2->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
$conditional2->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL);
$conditional2->addCondition('"NO"');
$conditional2->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
$conditional2->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$conditional2->getStyle()->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
$conditional2->getStyle()->getFill()->getEndColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

$conditionalStyles = $sheet->getStyle("G$rowStart:G$row")->getConditionalStyles();
$conditionalStyles[] = $conditional1;
$conditionalStyles[] = $conditional2;
$sheet->getStyle("G$rowStart:G$row")->setConditionalStyles($conditionalStyles);

$conditionalStyles = $sheet->getStyle("L$rowStart:L$row")->getConditionalStyles();
$conditionalStyles[] = $conditional1;
$conditionalStyles[] = $conditional2;
$sheet->getStyle("L$rowStart:L$row")->setConditionalStyles($conditionalStyles);

$fileName = "META_{$goal->apU}_{$goal->start_at}_{$goal->end_at}.xlsx";
$writer = new Xlsx($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
$writer->save('php://output');
    }
}
