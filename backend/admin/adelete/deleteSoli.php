<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  if(isset($id))
  {
    enviar("DELETE FROM textorno WHERE idex='$id'");
  }
 ?>
