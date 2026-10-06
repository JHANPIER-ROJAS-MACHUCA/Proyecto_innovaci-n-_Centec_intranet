<?php
require_once ("../conection/bdcredito.php");
extract($_POST);
if(!empty($id))
{
	if(enviar("DELETE FROM tahorro_motivo WHERE idam='$id'"))
  {
    echo json_encode(array("resultado"=>""));
  }
  else
  {
    echo json_encode(array("resultado"=>1));
  }
}
 ?>
