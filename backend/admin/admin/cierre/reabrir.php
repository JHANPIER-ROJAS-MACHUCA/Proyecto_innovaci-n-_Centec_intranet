<?php
include('../../../../conection/db7.php');
$idO=$_COOKIE['co_ido'];
$idU=$_COOKIE['co_id'];
extract($_POST);
if(isset($codigo))
{
  enviar("UPDATE tcaja_usuario SET montofin=NULL WHERE idCU='$codigo'");
  echo "1";
}
?>
