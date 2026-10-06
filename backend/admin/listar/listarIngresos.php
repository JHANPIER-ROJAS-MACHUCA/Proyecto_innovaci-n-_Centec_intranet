<?php

use CrediSoporte\Domain\Models\Transaction;

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$fecha = date('Y-m-d');
$idU = $_COOKIE['user1'];
//$idU='2';
//=============================
//============================

$billetaje = $database->table('tbilletaje')
  ->where('idU', $idU)
  ->where('fecha', $fecha)
  ->orderBy('idBille', 'desc')
  ->first();

$cash = $database->table('tcaja_usuario')
  ->where('idU', $idU)
  ->orderBy('idCA', 'desc')
  ->first();

$asignacionTransactions = Transaction::where('idCA', $cash->idCA)
  ->where('tipo', 1)
  ->where('habilitacion', 4)
  ->where('estadodt', 2)
  ->sum('monto');

$cobrosTransactions = Transaction::leftJoin('transaction_details', 'tcaja_usu_detal.idCAD', 'transaction_details.transaction_id')
  ->where('tcaja_usu_detal.idCA', $cash->idCA)
  ->where('tcaja_usu_detal.estadodt', 2)
  ->where('tcaja_usu_detal.tipo', 3)
  ->selectRaw('sum(if(transaction_details.id,tcaja_usu_detal.total,0)) as digital')
  ->selectRaw('sum(if(transaction_details.id,0,tcaja_usu_detal.total)) as cash')
  ->first();

$ahorroTransactions = Transaction::join('tahorro_deta', 'tcaja_usu_detal.idCuota', 'tahorro_deta.idAd')
  ->where('tcaja_usu_detal.idCA', $cash->idCA)
  ->where('tcaja_usu_detal.estadodt', 2)
  ->selectRaw('sum(if(tahorro_deta.tipo = 7, tcaja_usu_detal.total, 0)) as ingreso')
  ->selectRaw('sum(if(tahorro_deta.tipo = 8, tcaja_usu_detal.total, 0)) as egreso')
  ->first();

$incomeAndExpense = Transaction::join('tahorro_motivo', 'tcaja_usu_detal.tipo', 'tahorro_motivo.idam')
  ->where('tcaja_usu_detal.idCA', $cash->idCA)
  ->where('tcaja_usu_detal.estadodt', 2)
  ->whereNotIn('tcaja_usu_detal.tipo', [1, 2, 3])
  ->whereNull('tcaja_usu_detal.idCuota')
  ->select(
    'tcaja_usu_detal.idCAD',
    'tcaja_usu_detal.total',
    'tahorro_motivo.motivo'
  )
  ->selectRaw('if(tahorro_motivo.tipoM = 1, "INGRESO", "EGRESO") as tipo')
  ->get();

$desembolsoTransactions = Transaction::where('tcaja_usu_detal.idCA', $cash->idCA)
  ->where('tcaja_usu_detal.estadodt', 2)
  ->where('tcaja_usu_detal.tipo', 2)
  ->sum('total');

?>
<div class="col-md-12 col-sm-12">
  <div class="col-md-6">
    <div class="panel panel-info" style="padding:none">
      <div class="panel-heading">
        <h3 class="panel-title" align="center"><b>INGRESOS</b></h3>
      </div>
      <?php //ingresos
      $ingresos = $asignacionTransactions + $cobrosTransactions->cash + $ahorroTransactions->ingreso;
      ?>
      <div class="panel-body">
        <table class="table">
          <thead>
            <tr>
              <th>Tipo</th>
              <th>Monto</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>DESIGNACION DEL DIA</td>
              <td><?php echo $asignacionTransactions ?></td>
            </tr>
            <tr>
              <td>COBROS EN EFECTIVO</td>
              <td><?php echo $cobrosTransactions->cash ?></td>
            </tr>
            <tr>
              <td>COBROS EN DIGITAL</td>
              <td><?php echo $cobrosTransactions->digital ?></td>
            </tr>
            <tr>
              <td>AHORRO</td>
              <td><?php echo $ahorroTransactions->ingreso ?></td>
            </tr>
            <?php foreach ($incomeAndExpense->where('tipo', 'INGRESO') as $transaction) { ?>
              <?php $ingresos += $transaction->total ?>
              <tr>
                <td><?php echo $transaction->motivo ?></td>
                <td><?php echo $transaction->total ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="panel-footer">
        <div class="form-group row">
          <div align="right">
            <label class="col-md-8 control-label">TOTAL INGRESOS EN EFECTIVO S/.:</label>
          </div>
          <div class="col-md-4">
            <input type="text" value="<?php echo $ingresos ?>" class="form-control" style="text-align: right; font-weight: bold; color: red; background: white;" disabled>
          </div>
        </div>
        <div class="form-group row">
          <div align="right">
            <label class="col-md-8 control-label">TOTAL INGRESOS EN DIGITAL S/.:</label>
          </div>
          <div class="col-md-4">
            <input type="text" value="<?php echo $cobrosTransactions->digital ?>" class="form-control" style="text-align: right; font-weight: bold; color: red;background: white;" disabled>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php //egresos 
  $egresos = $desembolsoTransactions + $ahorroTransactions->egreso;
  ?>
  <div class="col-md-6">
    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title" align="center"><b>EGRESOS</b></h3>
      </div>
      <div class="panel-body">
        <table class="table">
          <thead>
            <tr>
              <th>Tipo</th>
              <th>Monto</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>DESEMBOLSOS</td>
              <td><?php echo $desembolsoTransactions ?></td>
            </tr>
            <tr>
              <td>AHORRO</td>
              <td><?php echo $ahorroTransactions->egreso ?></td>
            </tr>
            <?php foreach ($incomeAndExpense->where('tipo', 'EGRESO') as $transaction) { ?>
              <?php $egresos += $transaction->total ?>
              <tr>
                <td><?php echo $transaction->motivo ?></td>
                <td><?php echo $transaction->total ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="panel-footer">
        <div class="form-group row">
          <div align="right">
            <label class="col-md-6 control-label">TOTAL EGRESO S/.:</label>
          </div>
          <div class="col-md-6">
            <input type="text" value="<?php echo $egresos ?>" class="form-control" style="text-align: right; font-weight: bold; color: red; background: white;" disabled>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-md-6">
  <div class="form-group">
    <div class="col-md-12 col-sm-12">
      <label class="control-label">MONTO DISPONIBLE (S/.):</label>
      <input disabled type="text" style="text-align:center;color:blue;font-weight:bold;background:white" class="form-control input-sm" id="txtmontoDis" value="<?php echo round($ingresos - $egresos,2) ?>">
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-12 col-sm-12">
      <label class="control-label">MONTO BILLETAJE (S/.):</label>
      <input disabled style="text-align:center;color:blue;font-weight:bold;background:white" type="text" class="form-control input-sm" id="txtbilletaje" value="<?php echo number_format($billetaje->total,2) ?>">
    </div>
  </div>
</div>
<div class="col-md-6 col-sm-6">
  <div class="form-group">
    <label class="control-label">DIFERENCIA (S/.):</label>
    <input style="text-align:center;color:red;font-weight:bold;background:white" disabled type="text" class="form-control input-sm" id="txtdiferecnia" value="<?php echo round($billetaje->total - ($ingresos - $egresos), 2) ?>">
  </div>
</div>
<div align="right" class="panel-footer">
  <?php if (round($billetaje->total - ($ingresos - $egresos), 2) === 0.00 && !$cash->montofin) { ?>
    <button type="button" onclick="cerrarCaja()" class="btn btn-info">CERRAR CAJA</button>
  <?php } ?>
</div>