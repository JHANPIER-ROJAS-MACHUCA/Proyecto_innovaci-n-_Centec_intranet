<?php
include('../../../../conection/db7.php');
$idO=$_COOKIE['co_ido'];
$idU=$_COOKIE['co_id'];
extract($_POST);
function ordenFecha($dato)
{
  $f=preg_split("~/~",$dato);
  $fecha=$f[2]."-".$f[1]."-".$f[0];
  return $fecha;
}
if(isset($fecha))
{
  $fecha=ordenFecha($fecha);
  //optener la caja
  $ca=extraer("SELECT idCO as id,montoF as mon from tcaja_oficina where idO='$idO' and ini='$fecha'");
  $ra=mysqli_fetch_array($ca);
  $caja=$ra['id'];
  $cas=extraer("SELECT @id:='$caja' as id,(select  count(*) from tcaja_usuario where idc=@id ) as ini,(select  count(*) from tcaja_usuario where idc=@id and montofin is not null) as fin from tcaja_oficina limit 1");
  $rq=mysqli_fetch_array($cas);
  $cambio=0;
  $cambio=$rq['ini']-$rq['fin'];
  echo $cambio."|";

  if($ra['mon']=="")
  {
    $c=extraer("SELECT tu.idU as ide,montofin as fin FROM tcaja_usuario tcu inner join tusuario tu on tcu.idU=tu.idU where montofin is not null and idC='$caja'");
    while ($r=mysqli_fetch_array($c))
    {
      if($r['ide']!=$idU)
      {
        $saldo+=$r['fin'];
      }
    }
    echo $saldo;
  }
  else
  {
      echo "Información no disponible";
  }
}
 ?>
