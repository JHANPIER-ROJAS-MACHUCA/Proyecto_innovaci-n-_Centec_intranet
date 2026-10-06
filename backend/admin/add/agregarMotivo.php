<?php
include('../conection/bdcredito.php');
extract($_POST);
/*$motivo="dia de la madre";
$fecha="1";
$monto="3";*/
if(isset($motivo))
{
  $dato=extraer("select count(*) as dato from tahorro_motivo where motivo='$motivo'");
  $r=mysqli_fetch_array($dato);
  if($r['dato']=="0")
  {
    $motivo=strtoupper($motivo);

    $c=extraer("select count(*) as dato from tahorro_motivo");
    $da=mysqli_fetch_array($c);
    $cantidad=$da['dato'];
    $canti=number_format($cantidad, 2, '.', '');
    if($canti==0)
    {
      enviar("INSERT INTO tahorro_motivo (idam,motivo, fechas,monto) values ('9','$motivo','$fecha','$monto')");
    }
    else
    {
      if($id=="")
      {
        enviar("INSERT INTO tahorro_motivo (motivo, fechas,monto) values ('$motivo','$fecha','$monto')");
      }
      else
      {
        enviar("UPDATE tahorro_motivo SET motivo='$motivo', fechas='$fecha',monto='$monto' WHERE idam='$id'");
      }
    }
    echo json_encode(array("resultado"=>1));
  }
  else
  {
      echo json_encode(array("resultado"=>""));
  }
}
 ?>
