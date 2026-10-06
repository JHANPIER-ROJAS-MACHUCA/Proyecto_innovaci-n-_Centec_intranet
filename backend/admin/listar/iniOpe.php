<?php

require_once '../../vendor/autoload.php';
require_once '../../src/Domain/Database/bootstrap.php';

include('../conection/bdcredito.php');
extract($_POST);
if (isset($fechi) && isset($_COOKIE['user1']) && isset($_COOKIE['tofi'])) {
  $identiUsuario = $_COOKIE['user1'];
  $identiOficina = $_COOKIE['tofi'];

  //para ver si ya se abrio caja con la fecha actual
  $dat = preg_split("~/~", $fechi);
  $fech = $dat[2] . '-' . $dat[1] . '-' . $dat[0];

  $regi = $database->table('tcaja_oficina')->where('idO', $identiOficina)->where('ini', $fech)
    ->select('idCO as ide', 'monto as dinero')
    ->selectRaw('count(*) as dato')
    ->first();

  // $info=extraer("SELECT count(*) as dato,idCO AS ide,monto as dinero FROM tcaja_oficina where idO='$identiOficina' and ini='$fech' ");
  // $regi=mysqli_fetch_array($info);
  $resp = $regi->dato;
  $resp2 = $regi->ide;
  $abrio = $regi->dinero;

  //para ver si se cerro la caja del dia anterior
  $totalCash = $database->table('tcaja_oficina')->where('idO', $identiOficina)->whereNull('fin')->count();
  // $info2=extraer("SELECT count(*) as dato FROM tcaja_oficina where idO='$identiOficina' and fin is null");
  // $res=mysqli_fetch_array($info2);
  $resp3 = $totalCash;
  //si es 0 esta bien

  if ($resp == '1') {
    //obteer el saldo disponible
    // $database->table('tcaja_ofi_deta')->selectRaw('sum(monto) as resta')->where('idCO', $resp2)->first();
    $dife = (extraer("SELECT sum(monto) as resta FROM tcaja_ofi_deta where idCO='$resp2'"));
    $decodificando = mysqli_fetch_array($dife);
?>
    <div class="form-group row">
      <div class="col-md-4">
        <label class="form-control">SE ABRIO LA CAJA CON:<strong> S/. <?php echo $abrio; ?></strong></label>
        <input type="hidden" class="form-control" id="dinerOPE" value="<?php echo $abrio; ?>">
        <label class="form-control">SALDO DISPONIBLE: S/. <strong id="saldOPE"></strong></label>
        <input type="hidden" name="" id="saldOPE2" value="">
      </div>
    </div>
    <div class="form-group row">
      <form id="iniOperacionesF">
        <?php //usuario 
        ?>
        <div class="col-md-4">
          <?php //ide de transferencia 
          ?>
          <input type="hidden" name="idCO" id="idCO" value="<?php echo $resp2; ?>">
          <label>Usuario:</label>
          <select required class="form-control" id="UsuariOPE" name="UsuariOPE">
            <option value="" disabled selected>Seleccionar</option>
            <?php
            if (isset($_COOKIE['tofi'])) {
              $identiOficina = $_COOKIE['tofi'];
              $usersCash = $database->table('tusuario')
                ->select('idU as ide')
                ->selectRaw('concat_ws(" ", apU, amU, nomU) as dato')
                ->where('idO', $identiOficina)
                ->whereNotIn('tipoU', [1, 2])
                ->where('estadoU', '1')
                ->orderBy('apU', 'desc')
                ->get();
              // $usOpe = extraer("SELECT idU as ide,concat(apU,' ',amU,' ',nomU) as dato FROM tusuario where idO='$identiOficina' and (tipoU!='2' and tipoU!='1') and estadoU='1' order by apU asc");

              foreach ($usersCash as $item) {
            ?>
                <option value="<?php echo 'usu' . $item->ide ?>"><?php echo $item->dato ?></option>
            <?php
              }
            }
            ?>
          </select>
        </div>
        <?php //monto 
        ?>
        <div class="col-md-2">
          <label>Monto:</label>
          <div class="input-group margin">
            <span class="input-group-btn" disabled>
              <a class="btn btn-info" style="font-weight:bold">S/. </a>
            </span>
            <input autocomplete="off" onkeypress="return numi(event)" onkeyup="veriDiner()" title="Monto a Designar" required maxlength="7" class="form-control" style="text-align:right" placeholder="0.00" type="text" name="MontOPE" id="MontOPE" value="">
          </div>
        </div>
        <?php //tipo 
        ?>
        <div class="col-md-3" hidden>
          <label>Tipo:</label>
          <select title="Tipo de Desigancion" class="form-control" name="TipOPE" id="TipOPE">
            <option value="" disabled selected>- - Seleccione - -</option>
            <option value="1">Designado</option>
            <option value="2">Desembolso</option>
          </select>
        </div>
        <div class="col-md-3">
          <div class="" style="padding-top:22px">
            <?php
            $ofiidentici = $_COOKIE['tofi'];
            $infoa1 = extraer("  SELECT idCO FROM tcaja_oficina where idO='$ofiidentici' order by idCO desc limit 1");
            $reax = mysqli_fetch_array($infoa1);
            $idas = $reax['idCO'];
            $consulta = extraer("SELECT count(*) as resul FROM tcaja_oficina where idCO='$idas' and fin is not null");
            $dasa = mysqli_fetch_array($consulta);

            if ($dasa['resul'] == 0) {
            ?>
              <input type="submit" class="btn btn-md btn-info" name="" value="Guardar">
            <?php } ?>
            <input type="reset" class="btn btn-md btn-danger" name="" value="Limpiar">
          </div>
        </div>
      </form>
    </div>
    <div class="form-group row" id="iOPEHISTO" style="height: 250px;overflow: auto">

    </div>
  <?php
  } else if ($resp3 != '0') {
  ?>
    <div class="form-group row">
      <a class="btn btn-warning">Cierre la caja abierta anteriormente.</a>
    </div>
  <?php
  } else { //caso de que no se haya abierto aun la caja desde la fecha dad

  ?>
    <div class="form-group row">
      <button class="btn btn-sm btn-info" onclick="iniciaB()"><i class="fa fa-legal"></i>Empezar</button>
    </div>
<?php
  }
}
?>
<script src="functiones/aOpera1.js"></script>