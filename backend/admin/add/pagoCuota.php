<?php
  include("../conection/bdcredito.php");
  extract($_POST);
  date_default_timezone_set('america/lima');
  $fecha=date("Y-m-d");

  if(isset($id) && isset($mora) && isset($pago))
  {
    $identificado=$_COOKIE['user1'];
    enviar("UPDATE tpresta_detalle SET idU='$identificado',estado='1', montoPagado='$pago',pagoMora='$mora',fechaPago='$fecha' WHERE idPD='$id'");
  }
 ?>
