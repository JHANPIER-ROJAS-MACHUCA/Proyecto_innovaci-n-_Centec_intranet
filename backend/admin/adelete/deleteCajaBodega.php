<?php
include('../conection/bdcredito.php');
extract($_POST);
if(isset($idEli))
{
    enviar("DELETE FROM tcaja_bodega WHERE idBo='$idEli'");
}
 ?>
