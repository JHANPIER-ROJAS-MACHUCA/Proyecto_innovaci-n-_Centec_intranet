<?php
require_once ("../conection/bdcredito.php");
$id=intval($_GET['id']);
if(!empty($id))
{
	$imge=extraer("SELECT * from tprestamo where idP='$id'");
	$row =mysqli_fetch_array($imge);
  //eliminar imagen
	//unlink("../img2/user/".$row['img']);
	enviar("DELETE FROM tprestamo WHERE idP='$id'");
}
 ?>
