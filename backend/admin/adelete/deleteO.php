<?php
require_once ("../conection/bdcredito.php");
$id=intval($_GET['id']);
if(!empty($id))
{
	enviar("DELETE FROM toficina WHERE idO='$id'");
}
 ?>
