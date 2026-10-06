<?php
require_once ("../conection/bdcredito.php");
$id=intval($_GET['id']);
if(!empty($id))
{
	$imge=extraer("SELECT * from tvinculacion where idV='$id'");
	$row =mysqli_fetch_array($imge);
  //eliminar imagen
	unlink("../img2/user/".$row['croquisT']);
	enviar("DELETE FROM tvinculacion WHERE idV='$id'");
}
 ?>
