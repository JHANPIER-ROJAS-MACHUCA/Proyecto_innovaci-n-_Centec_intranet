<?php
  include('../conection/bdcredito.php');
$identificador=$_REQUEST['id'];
$cantidad=$_REQUEST['canti'];

$determina=extraer("SELECT if(pagoMora is null,0,pagoMora) as da FROM tpresta_detalle where idP='$identificador' and tfechaMora is null and pagoMora is not null  limit $cantidad");

$to=0;
while($r=mysqli_fetch_array($determina))
{
  $to+=$r['da'];
}

//$to=$cantidad*$mora;
echo json_encode(array("mor"=>"$to"));
 ?>
