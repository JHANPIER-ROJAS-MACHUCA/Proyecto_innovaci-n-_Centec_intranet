<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  $hora=date("H:m:s");
  $idU=$_COOKIE['user1'];
  $data=extraer("select idCA as id from tcaja_usuario where idU='$idU' order by idCA desc limit 1");
  $r=mysqli_fetch_array($data);
  $identificado=$r['id'];

  enviar("UPDATE tcaja_usuario SET montofin='$monto',hfin='$hora' where idCA='$identificado'");

 ?>
