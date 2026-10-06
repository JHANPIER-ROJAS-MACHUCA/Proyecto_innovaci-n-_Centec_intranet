<?php include('../conection/bdcredito.php');
  extract($_POST);
  if(isset($id))
  {
    enviar("UPDATE tcaja_usuario SET montofin=NULL , hfin=NULL WHERE idCA='$id'");
  }
?>
