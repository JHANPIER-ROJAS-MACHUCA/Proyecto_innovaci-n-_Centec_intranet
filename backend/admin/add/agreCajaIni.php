<?php

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

if (empty($_COOKIE['user1'])) {
  die();
}

$cashGerente = $database->table('tcaja_oficina')
  ->where('idO', 'CG')
  ->whereNull('montof')
  ->orderBy('idCO')
  ->first();

if (!is_null($cashGerente)) {
  // gerente ya abrio caja
  die();
}

$lastCashOffice = $database->table('tcaja_oficina')
  ->where('idO', '!=', 'CG')
  ->whereNOtNull('montof')
  ->orderBy('idCO', 'desc')
  ->first();

$database->table('tcaja_oficina')
  ->insert([
    'ini' => date('Y-m-d'),
    'iniH' => date('H:i:s'),
    'monto' => is_null($lastCashOffice) ? 0 : $lastCashOffice->monto,
    'idR' => $_COOKIE['user1'],
    'idO' => 'CG'
  ]);

$database->table('tbilletaje')->truncate();
