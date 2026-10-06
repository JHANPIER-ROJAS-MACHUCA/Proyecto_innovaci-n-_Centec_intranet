<?php
  include("../conection/bdcredito.php");
  extract($_POST);
  if(isset($ide) && isset($dis) && isset($ane) && isset($dir) && isset($ref))
  {
    enviar("INSERT INTO  tclie_direccion (idCG, iddis, anexo, direc, referencia) VALUES ('$ide','$dis','$ane','$dir','$ref') ");
  }
 ?>
