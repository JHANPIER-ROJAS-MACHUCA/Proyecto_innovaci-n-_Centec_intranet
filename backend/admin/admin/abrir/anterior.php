<?php
include('../../../../conection/db7.php');
$idO=$_COOKIE['co_ido'];
function desin($valor)
{
  $resul=0;
  if($valor!="")
  {
    $resul=$valor;
  }
  return $resul;
}
//consultamos la caja anterior cerrada
$con=extraer("SELECT if(montoF is null,monto,montoF) as codi FROM tcaja_oficina WHERE idO='$idO' order by idCO desc limit 1");
$r=mysqli_fetch_array($con);
$monto=desin($r['codi']);
echo $monto;
?>
