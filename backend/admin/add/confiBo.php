<?php
include('../conection/bdcredito.php');
extract($_POST);
if(isset($id))
{
  enviar("UPDATE tcaja_bodega SET estado='2' where idBo='$id'");
  $ca=extraer("SELECT count(*) as dato,idCO,monto FROM tcaja_oficina where idO='CG' and fin is null order by idCO desc");
  $c1=mysqli_fetch_array($ca);
  $identificador=$c1['idCO'];
  if($c1['dato']!="0")
  {
    enviar("UPDATE tcaja_bodega SET estadoConsumo='2' where idBo='$id'");
    $ca1=extraer("SELECT monto from tcaja_bodega where idBo='$id'");
    $ca2=mysqli_fetch_array($ca1);
    $valor=$ca2['monto'];
    enviar("UPDATE tcaja_oficina set monto=(monto+$valor) where idCO='$identificador'");
  }
}
?>
