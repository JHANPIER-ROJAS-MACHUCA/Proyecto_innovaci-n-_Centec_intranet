<?php

use CrediSoporte\Domain\Models\Transaction;
use CrediSoporte\Domain\Request\Request;

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$request = new Request();

$cashId = "";
$UsuariOPE = str_replace("usu", "", $request->UsuariOPE);

$cash = $database->table('tcaja_usuario')
  ->where('idCO', $request->idCO)
  ->where('idU', $UsuariOPE)
  ->first();

if (!$cash) {
  $cashId = $database->table('tcaja_usuario')
    ->insertGetId([
      'idCO' => $request->idCO,
      'idU' => $UsuariOPE
    ]);
} else {
  $cashId = $cash->idCA;
}

$transaction = new Transaction();
$transaction->idCA = $cashId;
$transaction->idR = $request->user()->idU;
$transaction->monto = $request->MontOPE;
$transaction->habilitacion = '1';
$transaction->created_at = date('Y-m-d H:i:s');
$transaction->save();