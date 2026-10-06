<?php
include('../conection/bdcredito.php');
$fecha=date('Y-m-d');
$idU= $_COOKIE['user1'];
extract($_POST);
if(isset($total))
{
  $d=extraer("SELECT count(*) as data FROM tbilletaje where fecha='$fecha' and idU='$idU'");
  $r=mysqli_fetch_array($d);

  //verificar si se abrio caja
  $dato=extraer("SELECT count(*) as data FROM tcaja_usuario where montoIni is not null and idU='$idU' and montofin is null");
  $f=mysqli_fetch_array($dato);

  if($r['data']=="0" && $f['data']=="1")
  {
    echo json_encode(array("resultado"=>1));
    enviar("INSERT INTO  tbilletaje (idU, b200, b100, b50, b20, b10, m5, m2, m1, m05, m02, m01, total, fecha) VALUES ('$idU','$b200','$b100','$b50','$b20','$b10','$b5','$b2','$b1','$b05','$b02','$b01','$total','$fecha')");
  }
  else
  {
      echo json_encode(array("resultado"=>""));
  }
}
else
{
  echo json_encode(array("resultado"=>""));
}
