
<?php
include ('conection/db2.php');
 ?>
 <?php
$db = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
?>

 <?php
if (isset($_POST) && count($_POST)>0)
{
	if ($db->connect_errno) 
	{
		die ("<span class='ko'>Fallo al conectar a MySQL: (" . $db->connect_errno . ") " . $db->connect_error."</span>");
	}
	else
	{
		$query=$db->query("update tclie_general set ".$_POST["campo"]."='".$_POST["valor"]."' where idCG='".intval($_POST["id"])."' limit 1");
		if ($query) echo "<span class='ok'>Valores modificados correctamente.</span>";
		else echo "<span class='ko'>".$db->error."</span>";
	}
}

if (isset($_GET) && count($_GET)>0)
{
	if ($db->connect_errno) 
	{
		die ("<span class='ko'>Fallo al conectar a MySQL: (" . $db->connect_errno . ") " . $db->connect_error."</span>");
	}
	else
	{
		?>
		 <?php
 $idcli = $_GET['idcli'];
 ?>
 <?php
		$query=$db->query("select * from tclie_general where idCG='$idcli'");
		$datos=array();
		while ($usuarios=$query->fetch_array())
		{
			$datos[]=array(	"id"=>$usuarios["idCG"],
							"nom"=>$usuarios["nom"],
							"ap"=>$usuarios["ap"],
							"correo"=>$usuarios["correo"],
							"telefono"=>$usuarios["telefono"],
							"cel"=>$usuarios["cel"],
								"direc"=>$usuarios["direc"]
			);
		}
		echo json_encode($datos);
	}
}
?>