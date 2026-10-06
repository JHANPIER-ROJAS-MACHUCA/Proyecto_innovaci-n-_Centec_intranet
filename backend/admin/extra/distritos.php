<?php

include('../conection/bdcredito.php');
extract($_POST);
$q = extraer("SELECT * FROM ta_dis where idprov='$prov'");

echo '<option value="" disabled selected>--Seleccione--</option>';

while ($row =mysqli_fetch_array($q))
{
	echo '<option value="' . $row['iddis']. '">' . $row['distri'] . '</option>' . "\n";
}
