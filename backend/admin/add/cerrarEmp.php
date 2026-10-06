<?php

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$saldo = $database->table('tcaja_oficina')
  ->where('idO', '!=', 'CG')
  ->sum('montof');

$database->table('tcaja_oficina')
  ->whereNull('montof')
  ->where('idO', 'CG')
  ->update([
    'fin' => date('Y-m-d'),
    'finH' => date('H:i:s'),
    'montof' => $saldo
  ]);
