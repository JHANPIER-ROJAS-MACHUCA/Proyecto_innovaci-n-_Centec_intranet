<?php
include('../../../../conection/db7.php');
$idO=$_COOKIE['co_ido'];
$idU=$_COOKIE['co_id'];
extract($_POST);
function desin($valor)
{
  $resul=0;
  if($valor!="")
  {
    $resul=$valor;
  }
  return $resul;
}
function ordenFecha($dato)
{
  $f=preg_split("~/~",$dato);
  $fecha=$f[2]."-".$f[1]."-".$f[0];
  return $fecha;
}
if(isset($fecha))
{
  $fecha=ordenFecha($fecha);
  //optener la caja
  $ca=extraer("SELECT idCO as id from tcaja_oficina where idO='$idO' and ini='$fecha'");
  $ra=mysqli_fetch_array($ca);
  $caja=$ra['id'];
  //optenemos el registro


 ?>
<?php //cierre de caja ?>
<div class="col-md-12 table-responsive">
  <div class="">
    <div class="col-sm-1 col-md-1">
        <label>Saldo: S/. <strong> </strong></label>
    </div>
    <div class="col-sm-3 col-md-3">
        <input type="text" class="form-control" id="montoCierre" style="text-align:center" disabled name="" value="<?php //echo $saldo ?>">
        <br>
    </div>
    <div class="col-sm-1 col-md-1">
        <label>Billetaje: S/. <strong> </strong></label>
    </div>
    <div class="col-sm-3 col-md-3">
        <input type="text" style="text-align:right" class="form-control" id="txtbilletajeAdmi" style="text-align:center" onkeypress="return numero(event)" name="" value="<?php //echo $saldo ?>">
        <br>
    </div>
    <div class="col-sm-4 col-md-4" style="padding-bottom:15px">
        <a id="btnContinuar" style="display:none" class="btn btn-primary" data-toggle="tab" href="#tab-3" onclick="detalleAdminCierre()"> <i class="fa fa-close"></i> Cerrar Caja</a>
    </div>
  </div>
  <table class="table table-striped" >
      <thead>
        <tr style="background:<?php echo $jua1['color']?> ;color:white;text-align:center">
          <th>
            USUARIO
          </th>
          <th>
            INICIO CAJA
          </th>
          <th>
            HORA INICIO
          </th>
          <th>
            CIERRE DE CAJA
          </th>

          <th>
          </th>
        </tr>
      </thead>
      <tbody >
        <?php
        $c=extraer("SELECT idCU as id, tu.idU as ide,datos,montoini as ini,hini as h,montofin as fin FROM tcaja_usuario tcu inner join tusuario tu on tcu.idU=tu.idU where montofin is not null and idC='$caja'");
        while ($r=mysqli_fetch_array($c))
        {
          if($r['ide']!=$idU)
          {
        ?>
          <tr id="caja<?php echo $r['id']?>">
            <td><?php echo $r['datos'] ?></td>
            <td><?php echo $r['ini'] ?></td>
            <td><?php echo $r['h'] ?></td>
            <td><?php echo $r['fin'] ?></td>
            <td><a class="btn btn-xs btn-primary" onclick="reabrirCaja(<?php echo $r['id'] ?>)">Abrir caja</a></td>
          </tr>
        <?php
          }
        }
        ?>
      </tbody>
  </table>
</div>
<?php
}
 ?>
