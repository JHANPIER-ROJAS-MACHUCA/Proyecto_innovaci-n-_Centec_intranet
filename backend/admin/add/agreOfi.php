<?php

include("../conection/bdcredito.php");
//editar
extract($_POST);

if(!empty($id))
{
	$txtdirec= strtoupper($dir);
	enviar("UPDATE toficina SET depa='$depa', prov='$prov', distrito='$dis', direccion='$txtdirec', telefono='$tel', correo='$ema' where idO='$id'");
}
else
{
		 $txtdirec= strtoupper($dir);
			enviar("INSERT INTO toficina (depa,prov, distrito, direccion, telefono, correo)	VALUES ('$depa','$prov','$dis','$txtdirec','$tel','$ema')");
		
}
?>
