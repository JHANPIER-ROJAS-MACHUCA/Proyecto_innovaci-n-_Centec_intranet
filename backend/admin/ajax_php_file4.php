<?php
require_once ("conection/bdcredito.php");
?>
<?php
// Storing source path of the file in a variable
 // Target path where file is to be stored
 // Moving Uploaded file
$idP=$_POST["txtidP5"];
$conyuge=$_POST["idconyuge"];
$aval=$_POST["idaval"];
	$V2=extraer("(select *  from tprestamo WHERE idP='$idP')");
  				$idV2 =mysqli_fetch_array($V2);
  				$idV = $idV2['idV'];
enviar("UPDATE tvinculacion SET  conyugue='$conyuge',aval='$aval' where idV='$idV'");
echo "<span id='success'>Vinculacion satisfactoriamente...!!</span><br/>";

?>