<?php

include('../conection/bdcredito.php');
extract($_POST);
if(!isset($tipoA))
{
  $q = extraer("SELECT idCG as id, concat(dni,' ',ap,' ',am,' ',nom) as datos FROM tclie_general order by ap asc");
}
else
{
  if($tipoA=='1')
  {
    $q = extraer("SELECT idCG as id, concat(dni,' ',ap,' ',am,' ',nom) as datos FROM tclie_general order by ap asc");
  }
  else
  {
    $q = extraer("SELECT idU as id, concat(dniU,' ',apU,' ',amU,' ',nomU) as datos FROM tusuario where tipoU!='7' order by apU asc");
  }
}

echo '<option value="" disabled selected>- - Seleccione - -</option>';

while ($row =mysqli_fetch_array($q))
{
	echo '<option value="' . $row['id']. '">' . $row['datos'] . '</option>' . "\n";
}
?>
