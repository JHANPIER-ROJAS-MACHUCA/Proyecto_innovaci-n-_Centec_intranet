<?php
include('../../../../conection/db7.php');
extract($_POST);
$idr=$_COOKIE['co_id'];
if(isset($idU))
{
  $c=extraer("SELECT idCU as id from tcaja_usuario where idU='$idU' and idC='$valor'");
  $r=mysqli_fetch_array($c);
  $iden=$r['id'];
  enviar("INSERT INTO tcaja_usuario_deta (idCU, idR, tipo, monto, estado) VALUES ('$iden','$idr','10','$monto','2')");
}
?>
