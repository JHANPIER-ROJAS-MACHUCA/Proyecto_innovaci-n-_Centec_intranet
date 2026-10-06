<?php

require_once ("../conection/bdcredito.php");
//editar
extract($_POST);
enviar("UPDATE toficina SET responsable='$idresponsa' where idO='$idddo'");
enviar("UPDATE tusuario SET idO='$idddo' where idU='$idresponsa'");
if(!empty($idresponsa))
{
	$f= date("Y-m-d ");
	enviar("INSERT INTO tresponsable (idO, idU, fecha) values ('$idddo','$idresponsa','$f')");
}
echo "<script>location.href='../agregarOficinas.php'</script>";
?>
