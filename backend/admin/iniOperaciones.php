<?php

// use CrediSoporte\Domain\Models\Transaction;

include('head.php');
if (isset($_COOKIE['tuser'])) {
  if ($_COOKIE['tuser'] == '1' || $_COOKIE['tuser'] == '7') {
    echo "<script>location.href='iniOperacionesPrinci.php'</script>";
  } else if ($_COOKIE['tuser'] != '2') {
    echo "<script>location.href='iniOperacionesUsua.php'</script>";
  }
}
$sal = "";
date_default_timezone_set('america/lima');
$f = date("d/m/Y");
if (isset($_COOKIE['tofi'])) {
  $identiOficina = $_COOKIE['tofi'];

  //reestrcturacion

  //consulta 1
  $sal = 0;
  $cajaofi = extraer("select count(*) as canti from tcaja_oficina where idO='$identiOficina'");
  $r1 = mysqli_fetch_array($cajaofi);
  $r1a = $r1['canti'];
  if ($r1a > 0) {
    //de bobeda
    $saldo = extraer("select if(montof!='',montof,monto) as saldo from tcaja_oficina where idO='$identiOficina' order by idCO desc limit 1");
    $row = mysqli_fetch_array($saldo);
    $sal = $row['saldo'];

    //de caja
    $saldo1 = extraer("SELECT if(sum(monto)!='',sum(monto),0) as saldo FROM tcaja_bodega where estado='2' and estadoConsumo='1' and idO='$identiOficina'");
    $row1 = mysqli_fetch_array($saldo1);
    $sal1 = $row1['saldo'];
    $sal = $sal + $sal1;
  } else {
    $saldo = extraer("SELECT if(sum(monto)!='',sum(monto),0) as saldo FROM tcaja_bodega where estado='2' and estadoConsumo='1' and idO='$identiOficina'");
    $row = mysqli_fetch_array($saldo);
    $sal = $row['saldo'];
  }

  //verificar si se abrio caja desde el gerente
  $verG = extraer("SELECT ini FROM tcaja_oficina where idO='CG' and fin is null");
  $esc = mysqli_fetch_array($verG);
  $fe = $esc['ini'] ?? null;
  $verificaciones = "";
  if ($fe != '' || $fe != null) {
    $dat = preg_split("~-~", $fe);
    $f = $dat[2] . '/' . $dat[1] . '/' . $dat[0];
    $verificaciones = $f;
  }
}


// ----------------------------------------
// require_once '../vendor/autoload.php';
// require_once '../app/database/database.php';
// require_once '../app/models/Transaction.php';

// $cash = $database::table('tcaja_usuario')
//   ->join('tcaja_oficina', 'tcaja_usuario.idCO', 'tcaja_oficina.idCO')
//   ->where('tcaja_usuario.idU', $_COOKIE['user1'])
//   ->whereNull('tcaja_usuario.hfin')
//   ->first();

// $asignacionTransactions = Transaction::where('idCA', 2906)
//   ->where('tipo', 1)
//   ->where('estadodt', 2)
//   ->where('habilitacion', 4)
//   ->sum('monto');

// $cobrosTransactions = Transaction::join('tclie_general', 'tcaja_usu_detal.cliente', 'tclie_general.idCG')
//   ->where('tcaja_usu_detal.idCA', 2906)
//   ->where('tcaja_usu_detal.estadodt', 2)
//   ->where('tcaja_usu_detal.tipo', 3)
//   ->sum('total');

// $incomeTransactions = Transaction::join('tahorro_motivo', 'tcaja_usu_detal.tipo', 'tahorro_motivo.idam')
//   ->where('tcaja_usu_detal.idCA', 2906)
//   ->where('tcaja_usu_detal.estadodt', 2)
//   ->whereNotIn('tcaja_usu_detal.tipo', [1, 2, 3])
//   ->where('tahorro_motivo.tipoM', 1)
//   ->sum('total');

// $desembolsoTransactions = Transaction::join('tclie_general', 'tcaja_usu_detal.cliente', 'tclie_general.idCG')
//   ->where('tcaja_usu_detal.idCA', 2906)
//   ->where('tcaja_usu_detal.estadodt', 2)
//   ->where('tcaja_usu_detal.tipo', 2)
//   ->sum('total');

// $expenseTransactions = Transaction::join('tahorro_motivo', 'tcaja_usu_detal.tipo', 'tahorro_motivo.idam')
//   ->where('tcaja_usu_detal.idCA', 2906)
//   ->where('tcaja_usu_detal.estadodt', 2)
//   ->whereNotIn('tcaja_usu_detal.tipo', [1, 2, 3])
//   ->where('tahorro_motivo.tipoM', 2)
//   ->sum('total');

?>
<?php //el primero es para la feha el seunda para los iconos 
?>
<link href="fecha/bootstrap-material-datetimepicker.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
<div class="panel panel-info" style="border-color:<?php echo $jua1['color'] ?>;">
  <div class="panel-heading" style="background-color:<?php echo $jua1['color'] ?>">
    <div class="btn-group pull-right">
      <!--<a accesskey="n" data-backdrop="static" data-toggle="modal" href='#agreUser' class="btn btn-primary"><i class="fa fa-plus-circle"></i>   Nuevo Ahorro</a>-->
    </div>
    <h5 style="color:white">Inicio de Operaciones <small style="color:black"> <?php echo $comentaJuve; ?></small></h5>
  </div>
  <div class="panel-body">
    <?php //monto designado por la bobeda 
    ?>
    <div class="form-group row">
      <div class="col-md-3">
        <label class="control-label">ABRIR CON:</label>
        <input type="hidden" name="sal" id="sal" value="<?php echo $sal; ?>">
        <input type="text" class="form-control" placeholder="Monto Designado" id="sald" name="sald" style="text-align:right;background:white;font-weight:bold" readonly value="<?php echo "S/.  " . $sal; ?>">
      </div>
      <?php //fecha 
      ?>
      <div class="col-md-3">
        <label class="control-label">FECHA :</label>
        <div class="input-group margin">
          <input type="text" class="form-control datepicker" placeholder="dd/mm/aaaa" name="fecha" id="fecha" style="text-align:center" value="<?php echo $f; ?>">
          <span class="input-group-btn">
            <a class="btn btn-info btn-flat" onclick="cambio()" style="height:34px;font-weight:bold"><i class="glyphicon glyphicon-search"></i></a>
          </span>
        </div>
      </div>
      <div class="col-md-3">
        <input type="hidden" class="form-control" id="fechi" value="">
      </div>
    </div>

    <div class="tabs-container">
      <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#tab-1" id="tituloAhorro">INICIAR</a></li>
        <li><a data-toggle="tab" href="#tab-2">CERRAR</a></li>
      </ul>
      <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
          <div class="panel-body">
            <?php
            if ($verificaciones != "") {
            ?>
              <label>Se abrio la caja con la fecha: <?php echo $f; ?></label>
            <?php
            }
            ?>
            <div id="iniciarB">

            </div>
          </div>
        </div>
        <div id="tab-2" class="tab-pane">
          <div class="panel-body">
            <div id="cerraronCaja">

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="functiones/aOpera.js"></script>
  <script src="extra/min.js"></script>

  <?php include('footer.php'); ?>
  <?php //para la fecha  
  ?>
  <script src="fecha/moment.js"></script>
  <script src="fecha/bootstrap-material-datetimepicker.js"></script>
  <script>
    $('.datepicker').bootstrapMaterialDatePicker({
      weekStart: 0,
      time: false,
      format: 'DD/MM/YYYY'
    });
  </script>