<?php

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Installment;

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$request['id'] = isset($_POST['id']) ? $_POST['id'] : '';
$request['mora'] = isset($_POST['mora']) ? $_POST['mora'] : 0;

$credit = Credit::find($_POST['id']);
$installments = Installment::where('idP', $_POST['id'])->get();

$cuotasConMora = 0;
$installmentWithMora = [];
foreach ($installments as $installment) {
  if ($installment->statusMora() === 'DEBT') {
    $cuotasConMora++;
    array_push($installmentWithMora, $installment);
  }
}

if (!is_numeric($request['mora'])) {
  echo json_encode([
    'message' => "El monto debe ser númerico",
    'success' => false
  ]);
  exit();
}

if ($cuotasConMora === 0) {
  echo json_encode([
    'message' => "El credito no tiene moras",
    'success' => false
  ]);
  exit();
}

if ($request['mora'] < 1) {
  echo json_encode([
    'message' => "El monto a ingresar debe ser mayor a igual a 1",
    'success' => false
  ]);
  exit();
}
if ($request['mora'] > $cuotasConMora) {
  echo json_encode([
    'message' => "El monto no debe ser mayor a " . $cuotasConMora,
    'success' => false
  ]);
  exit();
}

$exonerarMoras = array_slice($installmentWithMora, 0, $request['mora']);

$hoy = date('Y-m-d');
foreach ($exonerarMoras as $installment) {
  $installment->pagoMora = null;
  $installment->tfechaMora = $hoy;
  $installment->save();
}

echo json_encode([
  'message' => 'Se exonero ' . count($exonerarMoras) . ' moras.',
  'success' => true
]);
