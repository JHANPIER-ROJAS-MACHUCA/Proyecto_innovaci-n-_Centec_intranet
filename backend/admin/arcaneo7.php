<?php
set_time_limit(120);

include("conection/bdcredito.php");
//verificamos si pagaron o no su cuota
$a=extraer("SELECT tp.idP as id,sum(tpd.cuota) as pendiente,sum(montoPagado) as candelo ,if((sum(tpd.cuota) - sum(montoPagado))=0,'si','no') as cancelo FROM tprestamo tp inner join tpresta_detalle tpd on tp.idP=tpd.idP where tp.estado='4' group by tp.idP");//
while ($r=mysqli_fetch_array($a))
{
  $id=$r['id'];
  $resul=$r['cancelo'];
  if($resul=="si")
  {
    cuotaCompleta($id);
    eliminarMora($id);
    $fecha=fechadeCancelacion($id);
    actualizarPrestamo($id,$fecha);
    echo $id." ".$fecha." Eliminado<br>";
  }
  else if($resul=="no")
  {
    echo $id."No eliminado <br>";
  }
}
//estado del pago
function cuotaCompleta($id)
{
  $a=extraer("SELECT idPD as id, if(cuota-montoPagado=0,'si','no') as dato FROM tpresta_detalle where idP='$id'");
  while ($r=mysqli_fetch_array($a))
  {
    $iden=$r['id'];
    if($r['dato']=="no")
    {
      enviar("update tpresta_detalle set estado='1' where idPD='$iden'");
    }
  }
}
//eliminar moras papu
function eliminarMora($id)
{
  $a=extraer("SELECT idPD as id FROM tpresta_detalle where idP='$id' and pagoMora is not null and tfechaMora is null");
  while ($r=mysqli_fetch_array($a))
  {
    $ide=$r['id'];
    enviar("update tpresta_detalle set pagoMora=NULL where idPD='$ide'");
  }
}
//fecha de cancelacion
function fechadeCancelacion($id)
{
  $a=extraer("SELECT max(fechaPago) as dato FROM tpresta_detalle where idP='$id'");
  $r=mysqli_fetch_array($a);
  $fecha=$r['dato'];
  return $fecha;
}
//actualziar estado del prestamo
function actualizarPrestamo($id,$fecha)
{
  enviar("UPDATE tprestamo set estado='5', fechaTermino='$fecha' where idP='$id'");
}
 ?>
