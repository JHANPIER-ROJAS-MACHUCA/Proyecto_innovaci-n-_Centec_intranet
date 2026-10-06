<?php

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Models\Installment;

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$credit = Credit::find($_POST['id']);

$installments = Installment::where('idP', $_POST['id'])
  ->get();

$total = 0;
foreach ($installments as $installment) {
  if ($installment->statusMora() === 'DEBT') {
    $total += $credit->mora;
  }
}

echo $total;
