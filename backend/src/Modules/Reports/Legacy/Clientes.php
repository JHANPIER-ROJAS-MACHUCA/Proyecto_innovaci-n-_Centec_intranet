<?php

namespace CrediSoporte\Modules\Reports\Legacy;

use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Request\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Logica migrada desde app/exel/clientes.php
class Clientes
{
    public static function handle(): void
    {
$request = new Request();
$spreadsheet = new Spreadsheet();

$customers = Customer::leftJoin('tusuario', 'tclie_general.idU', 'tusuario.idU')
    ->leftJoin('dictionaries', 'tclie_general.risk_profile_id', 'dictionaries.id')
    ->leftJoin('dictionaries as _dictionarie_client_type', 'tclie_general.client_type', '_dictionarie_client_type.id')
    ->orderByRaw('concat_ws(" ",ap, am, nom)')
    ->select(
        'tclie_general.*',
        'dictionaries.description as risk_profile',
        '_dictionarie_client_type.description as client_type'
    )
    ->selectRaw('concat_ws(" ",tusuario.apU, tusuario.amU, tusuario.nomU) as funcionario')
    ->get();

$row = 1;

$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue("A$row", 'ID')
    ->setCellValue("B$row", 'Documento')
    ->setCellValue("C$row", 'Apellido paterno')
    ->setCellValue("D$row", 'Apellido Materno')
    ->setCellValue("E$row", 'Nombres')
    ->setCellValue("F$row", 'Genero')
    ->setCellValue("G$row", 'Direccion')
    ->setCellValue("H$row", 'Referencia')
    ->setCellValue("I$row", 'Celular')
    ->setCellValue("J$row", 'Correo')
    ->setCellValue("K$row", 'Rubro')
    ->setCellValue("L$row", 'Direccion de negocio')
    ->setCellValue("M$row", 'Hijos')
    ->setCellValue("N$row", 'Grado institucional')
    ->setCellValue("O$row", 'Tipo')
    ->setCellValue("P$row", 'Perfil de riesgo')
    ->setCellValue("Q$row", 'Fecha de nacimiento')
    ->setCellValue("R$row", 'Estado civil')
    ->setCellValue("S$row", 'Vivienda')
    ->setCellValue("T$row", 'Usuario')
    ->setCellValue("U$row", 'Latitud')
    ->setCellValue("V$row", 'Longitud')
    ->setCellValue("W$row", 'Ubigeo');

foreach ($customers as $customer) {
    $row++;

    $sheet->setCellValue("A$row", $customer->idCG);
    $sheet->setCellValue("B$row", $customer->dni);
    $sheet->setCellValue("C$row", $customer->ap);
    $sheet->setCellValue("D$row", $customer->am);
    $sheet->setCellValue("E$row", $customer->nom);
    $sheet->setCellValue("F$row", $customer->sexo);
    $sheet->setCellValue("G$row", $customer->direc);
    $sheet->setCellValue("H$row", $customer->referencia);
    $sheet->setCellValue("I$row", $customer->cel);
    $sheet->setCellValue("J$row", $customer->correo);
    $sheet->setCellValue("K$row", $customer->rubro);
    $sheet->setCellValue("L$row", $customer->telefono);
    $sheet->setCellValue("M$row", $customer->n_hijos);
    $sheet->setCellValue("N$row", $customer->grado_inst);
    $sheet->setCellValue("O$row", $customer->client_type);
    $sheet->setCellValue("P$row", $customer->risk_profile);
    $sheet->setCellValue("Q$row", $customer->fec_nac);
    $sheet->setCellValue("R$row", $customer->civilStatusToString());
    $sheet->setCellValue("S$row", $customer->tipo);
    $sheet->setCellValue("T$row", $customer->funcionario);
    $sheet->setCellValue("U$row", $customer->coordinate_lat);
    $sheet->setCellValue("V$row", $customer->coordinate_lng);
    $sheet->setCellValue("W$row", $customer->ubigeo_id);
}

// ajustamos las columas al contenido
$leters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
foreach ($leters as $leter) {
    $sheet->getColumnDimension($leter)->setAutoSize(true);
}

$fecha = date('Y-m-d');
$fileName = "Clientes_$fecha.xlsx";
$writer = new Xlsx($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
$writer->save('php://output');
    }
}
