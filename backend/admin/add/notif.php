<?php

use CrediSoporte\Domain\Request\Request;

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$request = new Request();

if (empty($_COOKIE['tuser'])) {
  die();
}

if ($_COOKIE['tuser'] === '2') {
  if ($request->post('acept')) {
    $cajaOficina = $database->table('tcaja_oficina')
      ->whereNull('fin')
      ->orderBy('idCO', 'desc')
      ->first();

    $cajaBobeda = $database->table('tcaja_bodega')
      ->where('idBo', $request->post('acept'))
      ->first();

    if (!is_null($cajaOficina) && !is_null($cajaBobeda)) {
      $database::beginTransaction();

      try {
        $database->table('tcaja_bodega')
          ->where('idBo', $request->post('acept'))
          ->update([
            'estado' => '2',
            'estadoConsumo' => '2'
          ]);

        $database->table('tcaja_oficina')
          ->where('idCO', $cajaOficina->idCO)
          ->update([
            'monto' => $cajaOficina->monto + $cajaBobeda->monto
          ]);

        $database::commit();
      } catch (\Throwable $th) {
        $database::rollback();
      }
    }
  }

  if ($request->post('recha')) {
    $database->table('tcaja_bodega')
      ->where('idBo', $request->post('recha'))
      ->update([
        'estado' => '3',
      ]);
  }
  if ($request->post('poste')) {
    $database->table('tcaja_bodega')
      ->where('idBo', $request->post('poste'))
      ->update([
        'estado' => '4',
      ]);
  }
} else {
  if ($request->post('acept')) {
    $transaction = $database->table('tcaja_usu_detal')
      ->where('idCAD', $request->post('acept'))
      ->first();

    if (!is_null($transaction)) {
      $database::beginTransaction();

      try {
        $database->table('tcaja_usu_detal')
          ->where('idCAD', $request->post('acept'))
          ->update([
            'habilitacion' => '4'
          ]);

        $database->table('tcaja_usuario')
          ->where('idCA', $transaction->idCA)
          ->update([
            'montoIni' => $database->table('tcaja_usuario')->where('idCA', $transaction->idCA)->first()->montoIni + $transaction->monto
          ]);

        $database::commit();
      } catch (\Throwable $th) {
        $database::rollback();
      }
    }
  }
  if ($request->post('recha')) {
    $database->table('tcaja_usu_detal')
      ->where('idCAD', $request->post('recha'))
      ->update([
        'habilitacion' => '3'
      ]);
  }
  if ($request->post('poste')) {
    $database->table('tcaja_usu_detal')
      ->where('idCAD', $request->post('poste'))
      ->update([
        'habilitacion' => '2'
      ]);
  }
}

$values = [];

if ($_COOKIE['tuser'] === '2') {
  $values = $database->table('tcaja_bodega')
    ->whereIn('estado', ['1', '4'])
    ->where('tipo', '2')
    ->select('idBo as id', 'monto as saldo')
    ->get();
} else {
  $values = $database->table('tcaja_oficina')
    ->join('tcaja_usuario', 'tcaja_oficina.idCO', 'tcaja_usuario.idCO')
    ->join('tcaja_usu_detal', 'tcaja_usuario.idCA', 'tcaja_usu_detal.idCA')
    ->where('tcaja_usuario.idU', $_COOKIE['user1'])
    ->whereNull('tcaja_oficina.fin')
    ->whereIn('tcaja_usu_detal.habilitacion', ['1', '2'])
    ->select('tcaja_usu_detal.idCAD as id', 'tcaja_usu_detal.monto as saldo')
    ->get();
}

if (!count($values) > 0) {
  die();
}
?>

<a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
  <i class="fa fa-bell"></i><span class="label label-primary"><?php echo count($values) ?></span>
  <input type="hidden" id="reconocimiento" value="<?php echo count($values) ?>">
</a>
<ul class="dropdown-menu dropdown-alerts">
  <?php foreach ($values as $value) { ?>
    <li>
      <div class="col-xs-12">
        <i class="fa fa-money"></i><?php echo "  S/. " . number_format($value->saldo, 2) ?>
        <span class="pull-right text-muted small">Monto designado a su usuario</span>
      </div>
      <div class="col-xs-3">
        <a class="btn btn-xs btn-info" onclick="confiB(<?php echo $value->id ?>)" onmouseout="this.style.background='#23c6c8';this.style.color='white'" onmouseover="this.style.background='white';this.style.color='black'" style="color:white;"><i class="fa fa-check-circle"></i></a>
      </div>
      <div class="col-xs-3">
        <a class="btn btn-xs btn-warning" onclick="rechaB(<?php echo $value->id ?>)" onmouseout="this.style.background='#f8ac59';this.style.color='white'" onmouseover="this.style.background='white';this.style.color='black'" style="color:white;"><i class="fa fa-times-rectangle"></i></a>
      </div>
      <div class="col-xs-3">
        <a class="btn btn-xs btn-success" onclick="posterB(<?php echo $value->id ?>)" onmouseout="this.style.background='#1c84c6';this.style.color='white'" onmouseover="this.style.background='white';this.style.color='black'" style="color:white;"><i class="fa fa-clock-o"></i></a>
      </div>
    </li>
    <li class="divider"></li>
  <?php } ?>
</ul>