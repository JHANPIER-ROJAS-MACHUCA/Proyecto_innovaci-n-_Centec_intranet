<?php
require_once ("../conection/bdcredito.php");
$id=intval($_GET['id']);
if(!empty($id))
{
	$imge=extraer("SELECT * from tusuario where idU='$id'");
	$row =mysqli_fetch_array($imge);
  //eliminar imagen
	unlink("../img2/user/".$row['img']);
	enviar("DELETE FROM tusuario WHERE idU='$id'");
}
 ?>
