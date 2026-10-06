<?php
  include("../conection/bdcredito.php");
  extract($_POST);
  if(isset($ide))
  {
    enviar("DELETE FROM tclie_negocio WHERE idCN='$ide'");    
  }
 ?>
