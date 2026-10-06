<?php
  include('../conection/bdcredito.php');
$identificador=$_REQUEST['id'];
$cantidad=$_REQUEST['cantidad'];

$determina=extraer("SELECT cuota,if(montoPagado is null,0,montoPagado) as da FROM tpresta_detalle where idP='$identificador' and (estado='2' or estado is null) limit $cantidad");

$to=0;
$to1=0;
while ($row=mysqli_fetch_array($determina))
{
  $to+=$row['cuota'];
  $to1+=$row['da'];
}
$final=$to-$to1;
echo json_encode(array("cuo"=>"$final"));
 ?>
