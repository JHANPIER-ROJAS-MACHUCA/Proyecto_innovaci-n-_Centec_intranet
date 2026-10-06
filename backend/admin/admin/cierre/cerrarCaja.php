<?php //datos importantes
include('../../../../conection/db7.php');
extract($_POST);
if(isset($idOfi) && isset($monto))
{
  $fecha=date("Y-m-d");
  $hora=date("H:i:s");
  enviar("UPDATE tcaja_oficina SET montoF='$monto',fin='$fecha',finH='$hora' where idCO='$idOfi'");
  echo "Se cerro la caja correctamente";
}
else {
  echo "Problemas en la operación";
}
?>
