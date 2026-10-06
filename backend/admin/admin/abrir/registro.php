<?php
include('../../../../conection/db7.php');
extract($_POST);
function ordenFecha($dato)
{
  $f=preg_split("~/~",$dato);
  $fecha=$f[2]."-".$f[1]."-".$f[0];
  return $fecha;
}
$fecha=ordenFecha($fecha);
if(isset($fecha))
{
  $c=extraer("SELECT idCUD as id, monto,datos,tcud.estado as estado from tcaja_usuario_deta tcud inner join tcaja_usuario tcu on tcud.idCU=tcu.idCU inner join tusuario tu on tcu.idU=tu.idU where fecha='$fecha' and tcud.tipo='10' order by idCUD desc");

  while ($r=mysqli_fetch_array($c))
  {
    //ESTADO
    $tipoEstado="";
    switch ($r['estado'])
    {
      case '1':
        $tipoEstado="CONFIRMADO";
        break;
      case '2':
        $tipoEstado="POR CONFIRMAR";
        break;
      case '3':
        $tipoEstado="RECHAZADO";
        break;
      case '4':
        $tipoEstado="EN ESPERA";
        break;
    }
 ?>
<tr id="desig<?php echo $r['id']?>">
  <td><?php echo $r['datos']?></td>
  <td><?php echo "S/. ".$r['monto']?></td>
  <td><?php echo "DESIGNACION";?></td>
  <td><?php echo $tipoEstado;?></td>
  <td>
    <?php
    if($r['estado']!="1")
    {
     ?>
     <a class="btn btn-sm btn-danger" onclick="Elimi(<?php echo $r['id']; ?>)"> <i class="glyphicon glyphicon-trash"></i> Eliminar</a></td>
    <?php
    }
     ?>
</tr>
<?php
 }
}
?>
