<?php
include("../conection/bdcredito.php");
extract($_POST);
if(isset($idEli) && isset($moti))
{
  enviar("DELETE FROM  tahorro_deta WHERE idAd='$idEli'");
  //conusltar quien coño es
  $c=extraer("SELECT idCAD as ide FROM tcaja_usu_detal where tipo='$tipo' and idCuota='$idEli' order by idCAD desc");
  $sa=mysqli_fetch_array($c);
  $id=$sa['ide'];
  enviar("UPDATE tcaja_usu_detal SET estadodt='1' where idCAD='$id'");

}
?>
