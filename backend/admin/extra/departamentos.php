<?php
include('../conection/bdcredito.php');

$q = extraer("SELECT * FROM ta_depa");

echo '<option value="" disabled selected>--Seleccione--</option>';

while ( $row =mysqli_fetch_array($q))
{
	echo '<option value="' . $row['iddepa']. '">' . $row['depa'] . '</option>' . "\n";
}
