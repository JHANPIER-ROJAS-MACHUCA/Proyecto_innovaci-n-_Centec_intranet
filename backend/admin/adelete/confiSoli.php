<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  if(isset($id))
  {
    enviar("update textorno set estado='1' WHERE idex='$id'");
  }
 ?>
