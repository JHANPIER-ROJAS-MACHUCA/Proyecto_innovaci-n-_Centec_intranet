<?php
  include('../../../../conection/db7.php');
  extract($_POST);
  //$fecha = date("Y-m-d",strtotime($fecha));

  $ho=date("H:i:s");
  $idO=$_COOKIE['co_ido'];
  $idU=$_COOKIE['co_id'];
  function ordenFecha($dato)
  {
    $f=preg_split("~/~",$dato);
    $fecha=$f[2]."-".$f[1]."-".$f[0];
    return $fecha;
  }
  function ordenFecha2($dato)
  {
    $f=preg_split("~-~",$dato);
    $fecha=$f[2]."/".$f[1]."/".$f[0];
    return $fecha;
  }
  function desin($valor)
  {
    $resul=0;
    if($valor!="")
    {
      $resul=$valor;
    }
    return $resul;
  }

//verificamos si gerente inicio dia o tiene un dia abierto
$cg=extraer("SELECT count(*) as co,ini from tcaja_oficina where idO='CG' and montoF is null order by idCO desc limit 1");
$rg=mysqli_fetch_array($cg);
if($rg['co']=="1")
{
  //echo "La fecha de inicio es ".ordenFecha2($rg['ini']);
  $fecha=$rg['ini'];
  //consultamos si hay caja abiertas anteriormente
  $caa=extraer("SELECT count(*) as co,ini from tcaja_oficina where idO='$idO' and montoF is null order by idCO desc limit 1");
  $rq=mysqli_fetch_array($caa);
  if($rq['co']=="0")
  {
    //consultamos si se abrio o no l caja en esa fecha
    $ca=extraer("SELECT count(*) as codi,montoF as can FROM tcaja_oficina where ini='$fecha' AND idO='$idO' order by idCO desc limit 1");
    $r1=mysqli_fetch_array($ca);
    if($r1['codi']==0)
    {
        //consultamos la caja anterior cerrada
        $con=extraer("SELECT montoF as codi FROM tcaja_oficina WHERE idO='$idO' order by idCO desc limit 1");
        $r=mysqli_fetch_array($con);
        $monto=desin($r['codi']);
        enviar("INSERT INTO tcaja_oficina (ini, iniH, monto, idR, idO) values ('$fecha','$ho','$monto','$idU','$idO')");
        echo "Dia iniciado con fecha ".ordenFecha2($fecha);
    }
    else
    {
      if(empty($r1['can']))
      {
        echo "La fecha ".ordenFecha2($fecha)." esta abierta aún";
      }
      else
      {
          echo "Ya la fecha ".ordenFecha2($fecha)." fue utilizada";
      }
    }
  }
  else
  {
    if($fecha==$rq['ini'])
    {
        echo "La fecha ".ordenFecha2($fecha)." esta abierta aún";
    }
    else
    {
        echo "Cierre la caja abierta el ".ordenFecha2($rq['ini']);
    }
  }
}
else
{
  echo "El gerente no inicio caja aún";
}

 ?>
