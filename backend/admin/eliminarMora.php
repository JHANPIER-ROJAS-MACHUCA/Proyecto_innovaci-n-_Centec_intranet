<?php
include("conection/bdcredito.php");
$c=extraer("SELECT * FROM tpresta_detalle where pagoMora is not null and tfechaMora is null limit 1");
while ($r=mysqli_fetch_array($c))
{
  $id=$r['idPD'];
  enviar("update tpresta_detalle set pagoMora=NULL where idPD='$id'");
  echo $id." Eliminado";
}
 ?>
