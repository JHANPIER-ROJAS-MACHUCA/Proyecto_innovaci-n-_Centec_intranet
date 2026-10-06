<?php
require_once ("conection/bdcredito.php");
?>
<?php
if(isset($_FILES["file3"]["type"]))
{
$validextensions = array("jpeg", "jpg", "png");
$temporary = explode(".", $_FILES["file3"]["name"]);
$file_extension = end($temporary);
if ((($_FILES["file3"]["type"] == "image/png") || ($_FILES["file3"]["type"] == "image/jpg") || ($_FILES["file3"]["type"] == "image/jpeg")
) && ($_FILES["file3"]["size"] < 100000)//Approx. 100kb files can be uploaded.
&& in_array($file_extension, $validextensions)) {
if ($_FILES["file3"]["error"] > 0)
{
echo "Return Code: " . $_FILES["file3"]["error"] . "<br/><br/>";
}
else
{
if (file_exists("upload/" . $_FILES["file3"]["name"])) {
echo $_FILES["file3"]["name"] . " <span id='invalid'><b>Archivo ya existe.</b></span> ";
}
else
{
$sourcePath = $_FILES['file3']['tmp_name']; // Storing source path of the file in a variable
$targetPath = "upload/".$_FILES['file3']['name']; // Target path where file is to be stored
move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
$idP=$_POST["txtidP4"];
	$V2=extraer("(select *  from tprestamo WHERE idP='$idP')");
  				$idV2 =mysqli_fetch_array($V2);
  				$idV = $idV2['idV'];
 $img=$_FILES["file3"]["name"];
enviar("UPDATE tvinculacion SET  croquisT='$img' where idV='$idV'");
echo "<span id='success'>Imagen subida satisfactoriamente...!!</span><br/>";
echo "<br/><b>Arhivo:</b> " . $_FILES["file3"]["name"] . "<br>";
echo "<b>Tipo:</b> " . $_FILES["file3"]["type"] . "<br>";
echo "<b>Tamaño:</b> " . ($_FILES["file3"]["size"] / 1024) . " kB<br>";
echo "<b>Archivo temporal:</b> " . $_FILES["file3"]["tmp_name"] . "<br>";
}
}
}
else
{
echo "<span id='invalid'>***Invalid file Size or Type***<span>";
}
}
?>