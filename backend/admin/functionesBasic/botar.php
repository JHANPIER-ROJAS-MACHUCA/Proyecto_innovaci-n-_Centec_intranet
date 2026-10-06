<?php
date_default_timezone_set('america/lima');
$hora=date("H:i");
$hora=strtotime($hora);
$valor=0;
switch ($datos['tipoU']) {
  case 2:
    $horaf=strtotime("19:20");
    if($hora>=$horaf)
    {
      $valor=1;
    }
    break;
    case 3:
      $horaf=strtotime("17:11");
      if($hora>=$horaf)
      {
        $valor=1;
      }
      break;
    case 4:
      $horaf=strtotime("18:50");
      if($hora>=$horaf)
      {
        $valor=1;
      }
    break;
}
if($valor==1)
{
  //echo json_encode(array("resultado"=>1));
}
else if($valor==0)
{
  //echo json_encode(array("resultado"=>1));
}
echo json_encode(array("resultado"=>1));
 ?>
