<?php require_once('../conection/conex.php'); ?>
<?php 
$id = $_REQUEST['id'];
$nombre_file = mktime().'.jpg';
$posicion = 0;
$origen = $_FILES['archivo']['tmp_name'];
$destino = "../img2/clie/$nombre_file";
move_uploaded_file($origen,$destino);

$insertSQL = ("UPDATE tclie_general SET imgC='".$nombre_file."' WHERE idCG=$id.");
	$Result1 = mysqli_query($conex, $insertSQL) or die(mysqli_error($conex));
	
	echo "<META HTTP-EQUIV=Refresh CONTENT=1;URL=../profile.php?idCG=$id>";
?>