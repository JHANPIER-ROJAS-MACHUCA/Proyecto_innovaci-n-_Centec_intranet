<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  $fecha=date("Y-m-d");
  $idusuario=$_COOKIE['user1'];
  if(isset($codigo) && isset($motivo))
  {
    $info=extraer("select count(*) as dato from tcaja_usu_detal where idCAD='$codigo' and estadodt='2'");
    $ro=mysqli_fetch_array($info);
    $resul=$ro['dato'];
    if($resul=='1')
    {
      $da=extraer("select count(*) as dato from textorno where cod='$codigo'");
      $r=mysqli_fetch_array($da);
      if($r['dato']=='0')
      {
        echo json_encode(array("resultado"=>1));
        enviar("insert into textorno (cod, motivo, fecha, idU) values ('$codigo','$motivo','$fecha','$idusuario')");
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

  }
 ?>
