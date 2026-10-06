<?php

use CrediSoporte\Domain\Request\Request;

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$request = new Request();

$fecha = implode("-", array_reverse(explode('/', $request->fechi)));


$cashOffice = $database->table('tcaja_oficina')
  ->where('idO', $_COOKIE['tofi'])
  // ->where('ini', $fecha)
  ->whereNull('fin')
  ->first();

if (!$cashOffice) {
  die();
}

$cash = $database->table('tcaja_usuario')
  ->where('idU', $request->user()->idU)
  ->where('idCO', $cashOffice->idCO)
  ->first();

if (!$cash) {
  die();
}

if ($cashOffice->ini !== $fecha) {
  echo "ESTA FECHA NO ESTA DISPONIBLE";
  die();
}
?>

<?php if ($cash->montoIni === null) { ?>
  <button class="btn btn-sm btn-info" onclick="iniciaCAU(<?php echo $cash->idCA ?>)">
    <i class="fa fa-legal"></i> Empezar
  </button>
  <?php die() ?>
<?php } ?>

<?php
$saldo = $database->table('tcaja_usu_detal')
  ->where('idCA', $cash->idCA)
  ->selectRaw('sum(monto) as saldo')
  ->first();
?>

<div>
  SE ABRIO CAJA CON <b>S/. <?php echo $saldo->saldo ?></b>
</div>

<script src="functionesBasic/operaUsu.js"></script>