<?php
include('../../../../conection/db7.php');
extract($_POST);
if(isset($codigo))
{
  $c=extraer("SELECT count(*) as dato,idCUD FROM tcaja_usuario_deta where idCUD='$codigo' and estado ='1'");
  $r=mysqli_fetch_array($c);
  if($r['dato']=='0')
  {
    enviar("DELETE FROM tcaja_usuario_deta where idCUD='$codigo'");
    echo "1";
  }
  else
  {
      echo "2";
  }
}

?>
