<?php

use CrediSoporte\Domain\Request\Request;

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$request = new Request();

$total = $database->table('tcaja_usu_detal')
  ->where('idCA', $request->iden)
  ->selectRaw('sum(monto) as saldo')
  ->first();

$database->table('tcaja_usuario')
->where('idCA', $request->iden)
->where('idU', $_COOKIE['user1'])
->update([
  'montoIni' => $total->saldo,
  'hini' => date('H:i:s')
]);