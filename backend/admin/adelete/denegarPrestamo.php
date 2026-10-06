<?php
  include("../conection/bdcredito.php");
  extract($_POST);
  //if(isset($id) && isset($motivo))
  //{
  $fd=extraer("SELECT estado FROM tprestamo WHERE idP='$id'");
  $fe=mysqli_fetch_array($fd);
  $estado=$fe['estado'];
  if($estado=='1')
  {
    enviar("UPDATE tprestamo set comentario='$motivo',estado='3' WHERE idP='$id'");
    echo "Credito Deengado";
  }
  else if($estado=='3' || $estado=='2')
  {
    enviar("UPDATE tprestamo set comentario='$motivo',estado='6' WHERE idP='$id'");
    echo "Credito Anulado";
  }
  else if($estado=='4')
  {
    $c=extraer("SELECT sum(montoPagado) as dato FROM tpresta_detalle where idP='$id'");
    $r=mysqli_fetch_array($c);
    $moto=$r['dato'];
    //===encontrar la caja abierta aun
    $re=extraer("SELECT idCAD,idCA FROM tcaja_usu_detal where conejo='$id' ");
    $re1=mysqli_fetch_array($re);
    $cajaUsuario=$re1['idCA'];
    $operacion=$re1['idCAD'];

    //===verificar caja aun abierta
    $reg=extraer("SELECT montofin as dato FROM tcaja_usuario where idCA='$cajaUsuario'");
    $re2=mysqli_fetch_array($reg);
    $contenido=$re2['dato'];
    if($contenido!="")
    {
      echo "Las abra la caja para hacer la anulacion";
    }
    else
    {
      if($dato==0 || $dato=="")
      {
        enviar("UPDATE tprestamo set comentario='$motivo',estado='6' WHERE idP='$id'");
        enviar("UPDATE tcaja_usu_detal set comentario='$motivo',estadodt='1' WHERE idCAD='$operacion'");
        echo "Credito Anulado";
      }
      else
      {
        echo "El credito ya tiene pagos realizados, comuniquese con el desarrollador".$monto;
      }
    }

  }

  //}

 ?>
