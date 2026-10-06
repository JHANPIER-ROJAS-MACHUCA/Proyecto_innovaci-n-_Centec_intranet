<?php
include('../conection/bdcredito.php');

extract($_POST);

if(isset($fechi) && isset($_COOKIE['user1']) && isset($_COOKIE['tofi']))
{
  $identiUsuario=$_COOKIE['user1'];
  $identiOficina=$_COOKIE['tofi'];

  //verificado la fecha abrierta por el gerente
  $verG=extraer("SELECT ini FROM tcaja_oficina where idO='CG' and fin is null");
  $esc=mysqli_fetch_array($verG);
  $fe=$esc['ini'];
  //verificando si se abrio caja en esa oficina
  $info=extraer("SELECT count(*) as dato FROM tcaja_oficina where idO='$identiOficina' and ini='$fe' ");
  $regi=mysqli_fetch_array($info);
  if($regi['dato']=='0')
  {

    //de caja cerrada
   $saldo=extraer("select if(count(*)>0,(select if(montof!='',montof,monto) from tcaja_oficina where idO='$identiOficina' order by idCO desc limit 1),0) as saldo from tcaja_oficina where idO='$identiOficina' order by idCO desc limit 1");
    $row =mysqli_fetch_array($saldo);
    $sal=$row['saldo'];

    //de caja
    $saldo1=extraer("SELECT if(sum(monto)!='',sum(monto),0) as saldo FROM tcaja_bodega where estado='2' and estadoConsumo='1' and idO='$identiOficina'");
    $row1 =mysqli_fetch_array($saldo1);
    $sal1=$row1['saldo'];
    $sal=$sal+$sal1;
    $sal=number_format($sal, 2, '.', '');
    //$monto=floattostr($sal);
      //optener el monto del cierre anterior
    /*  $info=extraer("SELECT count(*) as dato,montof as info FROM tcaja_oficina where idO='$identiOficina' order by idCO desc limit 1");
      $re=mysqli_fetch_array($info);
      if($re['dato']>0)
      {
        $monto=$re['info'];
      }*/
      $ho=date("H:i:s");
      enviar("INSERT INTO tcaja_oficina (ini,iniH,monto,idR,idO) VALUES ('$fe','$ho','$sal','$identiUsuario','$identiOficina')");
      //obtener la caja abierta
      $desa=extraer("select idCO as id1 from tcaja_oficina where idR='$identiUsuario' and idO='$identiOficina' order by idCO desc limit 1");
      $r=mysqli_fetch_array($desa);
      $cajaA=$r['id1'];

      enviar("INSERT INTO tcaja_usuario (idCO,tipo,idU) VALUES ('$cajaA','1','$identiUsuario')");
      $info=extraer("select idBo from tcaja_bodega where estadoConsumo='1' and estado='2' and idO='$identiOficina'");
      while ($row=mysqli_fetch_array($info))
      {
        $ide=$row['idBo'];
        enviar("update tcaja_bodega set estadoConsumo='2' where idBo='$ide'");
      }

  }

}
 ?>
