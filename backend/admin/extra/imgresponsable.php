<?php

include('../conection/bdcredito.php');
extract($_POST);
$q = extraer("SELECT img FROM tusuario where idU='$iduser'");

while ($row =mysqli_fetch_array($q))
{
	echo $row['img'];
}
?>
