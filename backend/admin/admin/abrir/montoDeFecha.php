<?php
include('../../../../conection/db7.php');
extract($_POST);
$idO=$_COOKIE['co_ido'];
function ordenFecha($dato)
{
  $f=preg_split("~/~",$dato);
  $fecha=$f[2]."-".$f[1]."-".$f[0];
  return $fecha;
}
$fecha=ordenFecha($fecha);
$c=extraer("SELECT if(monto is null,'',monto) as dato FROM tcaja_oficina where ini='$fecha' and idO='$idO'");
$r=mysqli_fetch_array($c);
echo $r['dato'];
?>
