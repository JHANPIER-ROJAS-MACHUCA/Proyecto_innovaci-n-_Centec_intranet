<?php
include('../../../../conection/db7.php');
extract($_POST);
if(isset($valor))
{

  $idU=$_COOKIE['co_id'];
  $saldo=0;
  $ca=extraer("SELECT idCU as id,montofin as  mon,idU from tcaja_usuario where idC='$valor'");
  while ($r=mysqli_fetch_array($ca))
  {
    if($r['mon']!="")
    {
      $saldo+=$r['mon'];
    }
    else
    {
      $codi=$r['id'];

      if($r['idU']==$idU)
      {
        $c=extraer("SELECT SUM(CASE WHEN tipo = '1' THEN monto ELSE 0 END) as compra,SUM(CASE WHEN tipo = '2' THEN monto ELSE 0 END) as venta,SUM(CASE WHEN tipo = '5' THEN monto ELSE 0 END) as adelanto,SUM(CASE WHEN tipo = '6' THEN monto ELSE 0 END) as cadel,SUM(CASE WHEN tipo = '7' THEN monto ELSE 0 END) as gastos,SUM(CASE WHEN tipo = '8' THEN monto ELSE 0 END) as ingresos,SUM(CASE WHEN tipo = '9' THEN monto ELSE 0 END) as ccredito,SUM(CASE WHEN tipo = '10' THEN monto ELSE 0 END) as designacion FROM tcaja_usuario_deta where idCU='$codi' and estado='1'");
        $r=mysqli_fetch_array($c);
        $ingresos=$r['venta']+$r['cadel']+$r['ingresos']+$r['ccredito']+$r['designacion'];
        $egresos=$r['compra']+$r['adelanto']+$r['gastos'];
        $disponible=$ingresos-$egresos;

        $saldo+=$disponible;

        $c=extraer("SELECT montoini as can FROM tcaja_usuario where idCU='$codi'");
        $r=mysqli_fetch_array($c);
        $saldo+=$r['can'];

      }
      else
      {
        $ca1=extraer("SELECT if(sum(monto) is null,0,sum(monto)) as mon FROM tcaja_usuario_deta where tipo='10' and (estado='1' or estado='2' or estado='4') and idCU='$codi'");
        while ($r1=mysqli_fetch_array($ca1))
        {
          $saldo-=$r1['mon'];
        }
      }
    }
  }
  echo $saldo;
}
 ?>
