<?php
include('../../../conection/db7.php');

extract($_POST);
$idU=$_COOKIE['co_ido'];
$fecha=date("Y-m-d");
enviar("INSERT INTO tmovi_deta (idmov, fecha, monto, idU) VALUES ('$id','$fecha','$monto','$idU')");
if($prov!="")
{
  enviar("UPDATE tproveedor SET direccion='$dir' where idprove='$prov'");
}
 ?>
