<?php
include('../../../../conection/db7.php');
extract($_POST);
$idO=$_COOKIE['co_ido'];
function ordenFecha($dato)
{
  $f=preg_split("~-~",$dato);
  $fecha=$f[2]."/".$f[1]."/".$f[0];
  return $fecha;
}
$c=extraer("SELECT if(ini is null,'',ini) as dato,idCO as id FROM tcaja_oficina where idO='$idO' and montoF is null order by idCO desc limit 1");
$r=mysqli_fetch_array($c);
if($r['dato']!="")
{
  echo ordenFecha($r['dato'])."|".$r['id'];
}
else
{
  echo "|";
}
?>
