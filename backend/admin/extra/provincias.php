<?php

include('../conection/bdcredito.php');
extract($_POST);
$q = extraer("SELECT * FROM ta_prov where iddepa='$depa'");

echo '<option value="" disabled selected>--Seleccione--</option>';

while ($row =mysqli_fetch_array($q))
{
	echo '<option value="' . $row['idprov']. '">' . $row['provi'] . '</option>' . "\n";
}
?>
