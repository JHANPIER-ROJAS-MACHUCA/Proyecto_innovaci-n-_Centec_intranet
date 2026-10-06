<?php

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

if (empty($_POST['monto']) || empty($_POST['motivo']) || empty($_POST['tipo'])) {
  die();
}

$cash = $database->table('tcaja_usuario')
  ->join('tcaja_oficina', 'tcaja_usuario.idCO', 'tcaja_oficina.idCO')
  ->where('tcaja_usuario.idU', $_COOKIE['user1'])
  ->where('tcaja_oficina.idO', $_COOKIE['tofi'])
  ->whereNull('tcaja_usuario.montofin')
  ->whereNull('tcaja_oficina.montof')
  ->first();

if (is_null($cash)) {
  die();
}


echo $database->table('tcaja_usu_detal')
  ->insertGetId([
    'idCA' => $cash->idCA,
    'tipo' => $_POST['tipo'],
    'total' => $_POST['monto'],
    'comentario' => $_POST['motivo'],
    'created_at' => date('Y-m-d')
  ]);
