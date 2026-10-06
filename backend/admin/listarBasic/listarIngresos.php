<?php

use CrediSoporte\Domain\Models\Transaction;

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

$fecha = date('Y-m-d');
$idOfi = $_COOKIE['tofi'];

$cash = $database::table('tcaja_usuario')
  ->join('tcaja_oficina', 'tcaja_usuario.idCO', 'tcaja_oficina.idCO')
  ->where('tcaja_oficina.idO', $idOfi)
  ->where('tcaja_usuario.tipo', 1)
  ->orderBy('tcaja_usuario.idCA', 'desc')
  ->first();

$designaciones = 0;
if ($cash->tipo == 1) {
  $designaciones = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
    ->where('tcaja_usuario.idCO', $cash->idCO)
    ->where('tcaja_usu_detal.tipo', '1')
    ->where('tcaja_usu_detal.habilitacion', '4')
    ->sum('tcaja_usu_detal.monto');
}

$asignacionTransactions = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
  ->where('tcaja_usuario.idCO', $cash->idCO)
  ->where('tcaja_usu_detal.tipo', 1)
  ->where('tcaja_usu_detal.estadodt', 2)
  ->where('tcaja_usu_detal.habilitacion', 4)
  ->get();

$cobrosTransactions = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
  ->leftJoin('transaction_details', 'tcaja_usu_detal.idCAD', 'transaction_details.transaction_id')
  ->where('tcaja_usuario.idCO', $cash->idCO)
  ->where('tcaja_usu_detal.estadodt', 2)
  ->where('tcaja_usu_detal.tipo', 3)
  ->select(
    'tcaja_usu_detal.idCA',
    'tcaja_usu_detal.total'
  )
  ->selectRaw('if(transaction_details.id, "DIGITAL", "CASH") as tipo')
  ->get();

$ahorroTransactions = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
  ->join('tahorro_deta', 'tcaja_usu_detal.idCuota', 'tahorro_deta.idAd')
  ->join('tahorro_motivo', 'tahorro_deta.moti', 'tahorro_motivo.idam')
  ->join('tclie_general', 'tcaja_usu_detal.cliente', 'tclie_general.idCG')
  ->where('tcaja_usuario.idCO', $cash->idCO)
  ->where('tcaja_usu_detal.estadodt', 2)
  ->select(
    'tcaja_usu_detal.idCAD',
    'tcaja_usu_detal.idCA',
    'tcaja_usu_detal.total',
    'tahorro_deta.tipo',
  )
  ->selectRaw('concat_ws(" ", tclie_general.ap, tclie_general.am, tclie_general.nom) as customer')
  ->selectRaw('if(tahorro_deta.tipo = 7,"INGRESO","EGRESO") as tipo')
  ->get();

$incomeAndExpense = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
  ->join('tahorro_motivo', 'tcaja_usu_detal.tipo', 'tahorro_motivo.idam')
  ->where('tcaja_usuario.idCO', $cash->idCO)
  ->where('tcaja_usu_detal.estadodt', 2)
  ->whereNotIn('tcaja_usu_detal.tipo', [1, 2, 3])
  ->whereNull('tcaja_usu_detal.idCuota')
  ->select(
    'tcaja_usu_detal.idCAD',
    'tcaja_usu_detal.total',
    'tahorro_motivo.motivo',
  )
  ->selectRaw('if(tahorro_motivo.tipoM = 1, "INGRESO", "EGRESO") as tipo')
  ->get();

$desembolsoTransactions = Transaction::join('tcaja_usuario', 'tcaja_usu_detal.idCA', 'tcaja_usuario.idCA')
  ->where('tcaja_usuario.idCO', $cash->idCO)
  ->where('tcaja_usu_detal.estadodt', 2)
  ->where('tcaja_usu_detal.tipo', 2)
  ->get();

$usersIncomeCash = 0;
$usersIncomeDigital = 0;
$usersExpense = 0;

$incomeCash = 0;
$incomeDigital = 0;
$expense = 0;

?>

<div class="col-md-12 col-sm-12">
  <div class="">
    <label>Usuarios </label>
  </div>
  <div class="col-md-6">
    <div class="panel panel-info" style="padding:none">
      <div class="panel-heading">
        <h3 class="panel-title" align="center"><b>INGRESOS DE LOS USUARIOS</b></h3>
      </div>
      <?php //ingresos 
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
              <td>
                <?php
                $asignacion = $asignacionTransactions->where('idCA', '!=', $cash->idCA)->sum('monto');
                $usersIncomeCash += $asignacion;
                echo number_format($asignacion, 2);
                ?>
              </td>
            </tr>
            <tr>
              <td>COBROS DE PRESTAMO EN EFECTIVO</td>
              <td>
                <?php
                $cobrosCash = $cobrosTransactions->where('idCA', '!=', $cash->idCA)->where('tipo', 'CASH')->sum('total');
                $usersIncomeCash += $cobrosCash;
                echo number_format($cobrosCash, 2);
                ?>
              </td>
            </tr>
            <tr>
              <td>COBROS DE PRESTAMO EN DIGITAL</td>
              <td>
                <?php
                $usersIncomeDigital = $cobrosTransactions->where('idCA', '!=', $cash->idCA)->where('tipo', 'DIGITAL')->sum('total');
                echo number_format($usersIncomeDigital, 2);
                ?>
              </td>
            </tr>
            <?php foreach ($incomeAndExpense->where('idCA', '!=', $cash->idCA)->where('tipo', 'INGRESO') as $income) { ?>
              <?php $usersIncomeCash += $income->total ?>
              <tr>
                <td><?php echo $income->motivo ?></td>
                <td><?php echo number_format($income->total, 2) ?></td>
              </tr>
            <?php } ?>
            <tr>
              <td>AHORRO</td>
              <td>
                <?php
                $incomeAhorro = $ahorroTransactions->where('idCA', '!=', $cash->idCA)->where('tipo', 'INGRESO')->sum('total');
                $usersIncomeCash += $incomeAhorro;
                echo number_format($incomeAhorro, 2);
                ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="panel-footer">
        <div class="form-group row">
          <div align="right">
            <label class="col-md-8 control-label">TOTAL INGRESOS EN EFECTIVO S/.:</label>
          </div>
          <div class="col-md-4">
            <input style="background: white;color:red;font-weight:bold;text-align:right" disabled type="text" class="form-control input-sm" id="txtingreso" value="<?php echo number_format($usersIncomeCash, 2); ?>">
          </div>
        </div>
        <div class="form-group row">
          <div align="right">
            <label class="col-md-8 control-label">TOTAL INGRESOS EN DIGITAL S/.:</label>
          </div>
          <div class="col-md-4">
            <input style="background: white;color:red;font-weight:bold;text-align:right" disabled type="text" class="form-control input-sm" id="txtingreso" value="<?php echo number_format($usersIncomeDigital, 2); ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php //egresos 
  ?>
  <div class="col-md-6">
    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title" align="center"><b>EGRESOS DE LOS USUARIOS</b></h3>
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
              <td>DESEMBOLSO</td>
              <td>
                <?php
                $desembolsos = $desembolsoTransactions->where('idCA', '!=', $cash->idCA)->sum('total');
                $usersExpense += $desembolsos;
                echo number_format($desembolsos, 2);
                ?>
              </td>
            </tr>
            <tr>
              <td>AHORRO</td>
              <td>
                <?php
                $ahorro = $ahorroTransactions->where('idCA', '!=', $cash->idCA)->where('tipo', 'EGRESO')->sum('total');
                $usersExpense += $ahorro;
                echo number_format($ahorro, 2);
                ?>
              </td>
            </tr>
            <?php foreach ($incomeAndExpense->where('idCA', '!=', $cash->idCA)->where('tipo', 'EGRESO') as $income) { ?>
              <?php $usersExpense += $income->total ?>
              <tr>
                <td><?php echo $income->motivo ?></td>
                <td><?php echo number_format($income->total, 2) ?></td>
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
            <input style="background: white;color:red;font-weight:bold;text-align:right" disabled type="text" class="form-control input-sm" id="txtegreso" value="<?php echo number_format($usersExpense, 2); ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-md-12 col-sm-12">
  <div class="">
    <label>Administrador</label>
  </div>
  <div class="col-md-6">
    <div class="panel panel-info" style="padding:none">
      <div class="panel-heading">
        <h3 class="panel-title" align="center"><b>INGRESOS DE LOS ADMINISTRADOR</b></h3>
      </div>
      <?php //ingresos 
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
              <td>INICIO</td>
              <td>
                <?php
                $incomeCash += $cash->monto;
                echo number_format($cash->monto, 2);
                ?>
              </td>
            </tr>
            <tr>
              <td>DESIGNACION DEL DIA</td>
              <td>
                <?php
                $asignacion = $asignacionTransactions->where('idCA', $cash->idCA)->sum('monto');
                $incomeCash += $asignacion;
                echo number_format($asignacion, 2);
                ?>
              </td>
            </tr>
            <tr>
              <td>COBROS DE PRESTAMO EN EFECTIVO</td>
              <td>
                <?php
                $cobrosCash = $cobrosTransactions->where('idCA', $cash->idCA)->where('tipo', 'CASH')->sum('total');
                $incomeCash += $cobrosCash;
                echo number_format($cobrosCash, 2);
                ?>
              </td>
            </tr>
            <tr>
              <td>COBROS DE PRESTAMO EN DIGITAL</td>
              <td>
                <?php
                $incomeDigital = $cobrosTransactions->where('idCA', $cash->idCA)->where('tipo', 'DIGITAL')->sum('total');
                echo number_format($incomeDigital, 2);
                ?>
              </td>
            </tr>
            <?php foreach ($incomeAndExpense->where('idCA', $cash->idCA)->where('tipo', 'INGRESO') as $income) { ?>
              <?php $incomeCash += $income->total ?>
              <tr>
                <td><?php echo $income->motivo ?></td>
                <td><?php echo number_format($income->total, 2) ?></td>
              </tr>
            <?php } ?>
            <tr>
              <td>AHORRO</td>
              <td>
                <?php
                $incomeAhorro = $ahorroTransactions->where('idCA', $cash->idCA)->where('tipo', 'INGRESO')->sum('total');
                $incomeCash += $incomeAhorro;
                echo number_format($incomeAhorro, 2);
                ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="panel-footer">
        <div class="form-group row">
          <div align="right">
            <label class="col-md-6 control-label">TOTAL INGRESOS S/.:</label>
          </div>
          <div class="col-md-6">
            <input style="background: white;color:red;font-weight:bold;text-align:right" disabled type="text" class="form-control input-sm" id="txtingreso" value="<?php echo number_format($incomeCash, 2) ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php //egresos 
  ?>
  <div class="col-md-6">
    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title" align="center"><b>EGRESOS DEL ADMINISTRADOR</b></h3>
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
              <td>DESIGNACIONES</td>
              <td>
                <?php
                $expense += $designaciones;
                echo number_format($designaciones, 2);
                ?>
              </td>
            </tr>
            <tr>
              <td>DESEMBOLSO</td>
              <td>
                <?php
                $desembolsos = $desembolsoTransactions->where('idCA', $cash->idCA)->sum('total');
                $expense += $desembolsos;
                echo number_format($desembolsos, 2);
                ?>
              </td>
            </tr>
            <tr>
              <td>AHORRO</td>
              <td>
                <?php
                $ahorro = $ahorroTransactions->where('idCA', $cash->idCA)->where('tipo', 'EGRESO')->sum('total');
                $expense += $ahorro;
                echo number_format($ahorro, 2);
                ?>
              </td>
            </tr>
            <?php foreach ($incomeAndExpense->where('idCA', $cash->idCA)->where('tipo', 'EGRESO') as $income) { ?>
              <?php $expense += $income->total ?>
              <tr>
                <td><?php echo $income->motivo ?></td>
                <td><?php echo number_format($income->total, 2) ?></td>
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
            <input style="background: white;color:red;font-weight:bold;text-align:right" disabled type="text" class="form-control input-sm" id="txtegreso" value="<?php echo number_format($expense, 2) ?>">
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
      <input disabled type="text" style="text-align:center;color:blue;font-weight:bold;background:white" class="form-control input-sm" id="txtmontoDis" value="<?php echo $usersIncomeCash - $usersExpense + $incomeCash - $expense; ?>">
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-12 col-sm-12">
      <label class="control-label">MONTO BILLETAJE (S/.):</label>
      <input disabled style="text-align:center;color:blue;font-weight:bold;background:white" type="text" class="form-control input-sm" id="txtbilletaje" value="">
    </div>
  </div>
</div>
<div class="col-md-6 col-sm-6">
  <div class="form-group">
    <label class="control-label">DIFERENCIA (S/.):</label>
    <input style="text-align:center;color:red;font-weight:bold;background:white" disabled type="text" class="form-control input-sm" id="txtdiferencia" value="">
  </div>
</div>
<div align="right" class="panel-footer">
  <?php if (!$cash->fin) { ?>
    <button type="button" id="btnCerrarCaja" disabled onclick="confirm('Estas seguro de Cerrar Caja?', 'Title',cerrarOficina())" class="btn btn-info">CERRAR CAJA</button>
  <?php } ?>
</div>