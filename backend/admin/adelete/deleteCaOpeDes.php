<?php
include('../conection/bdcredito.php');
extract($_POST);

if(isset($valor))
{
  //enviar("DELETE FROM tcaja_ofi_deta where idCOD='$valor'");
  enviar("DELETE FROM tcaja_usu_detal WHERE idCAD='$valor'");
}
 ?>
