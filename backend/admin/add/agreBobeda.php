<?php
require_once('../conection/bdcredito.php');
extract($_POST);
$usuario=$_COOKIE['user1'];
if( isset($monto))
{
  $f=date("Y-m-d ");
  if($tipo==1)
  {
    enviar("INSERT INTO tcaja_bodega (fecha, monto, idU, tipo, estado) VALUES ('$f','$monto','$usuario','$tipo','1')");
  }
  else
  {
    enviar("INSERT INTO tcaja_bodega (fecha, monto, idO, idU, tipo, estado) VALUES ('$f','$monto','$Ofic','$usuario','$tipo','1')");
  }

}
 ?>
