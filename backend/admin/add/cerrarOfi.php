<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  $hora=date("H:m:s");
  $fecha=date("Y-m-d");
  if(isset($_COOKIE['tofi']))
  {
    $idO=$_COOKIE['tofi'];
    $data=extraer("SELECT idCO as id FROM tcaja_oficina where idO='$idO' and montof is null");
    $r=mysqli_fetch_array($data);
    $id=$r['id'];
    if(isset($monto))
    {
      enviar("UPDATE tcaja_oficina SET fin='$fecha', finH='$hora', montof='$monto' where idCO='$id'");
    }
  }
 ?>
