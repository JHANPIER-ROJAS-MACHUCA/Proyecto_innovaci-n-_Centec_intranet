<?php
require_once ("conection/bdcredito.php");
?>
<?php 
$id = $_POST['id'];
//eliminar
$U=extraer("(select *  from tclie_general WHERE idCG='$id')");
  				$idUimg =mysqli_fetch_array($U);
  				$img = $idUimg['imgC'];
$dir = "img2/clie/"; /*Ruta local donde se almacenan tu imagen*/
  $file = $img; /* Nombre de tu imagen */
  ?>
  <?php 
$nombre_archivo = $dir."/".$file; 
if (file_exists($nombre_archivo)) { 
     	unlink($dir.$file); 
} else { 
    echo "El archivo  no existe"; 
} 
?> 
<?php
  /* Eliminas tu Imagen*/
//
$nombre_file = mktime().'.jpg';
$posicion = 0;
$origen = $_FILES['archivo']['tmp_name'];
$destino = "img2/clie/$nombre_file";
move_uploaded_file($origen,$destino);

enviar("UPDATE tclie_general SET  imgC='$nombre_file' where idCG='$id'");
?>
 <div class="alert alert-info">
                Modificado correctamente.
            </div>