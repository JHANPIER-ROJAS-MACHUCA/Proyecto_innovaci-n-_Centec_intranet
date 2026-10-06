<?php
require_once ("../conection/bdcredito.php");
$id=intval($_POST['ide']);
if(!empty($id))
{
	enviar("DELETE FROM tclie_direccion WHERE idCD='$id'");
}
 ?>
