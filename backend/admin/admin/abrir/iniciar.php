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
$c=extraer("SELECT direccion as dir,ini,iniH,monto FROM tcaja_oficina tco inner join toficina tof on tco.idO=tof.idO where ini='$fecha' ");
while ($r=mysqli_fetch_array($c))
{
  ?>
    <tr>
      <td>
        <?php echo $r['dir']; ?>
      </td>
      <td>
        <?php echo $r['monto']; ?>
      </td>
      <td>
        <?php echo $r['ini']; ?>
      </td>
      <td>
        <?php echo $r['iniH']; ?>
      </td>
    </tr>
  <?php
}
 ?>
