<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  /*$motivo="SADas0";
  $tipo="2";
  $id="";*/
  if(isset($motivo) && isset($tipo))
  {
    $motivo=strtoupper($motivo);
    $c=extraer("select count(*) as dato from tahorro_motivo");
    $da=mysqli_fetch_array($c);
    $cantidad=$da['dato'];
    $canti=number_format($cantidad, 2, '.', '');
   if(empty($id))
    {
      if($canti==0)
      {
        enviar("INSERT INTO tahorro_motivo (idam,motivo,tipoM) VALUES ('9','$motivo','$tipo')");
      }
      else
      {
          enviar("INSERT INTO tahorro_motivo (motivo,tipoM) VALUES ('$motivo','$tipo')");
      }
    }
    else
    {
      enviar("UPDATE tahorro_motivo SET motivo='$motivo',tipoM='$tipo' WHERE idam='$id'");
    }
  }
?>
