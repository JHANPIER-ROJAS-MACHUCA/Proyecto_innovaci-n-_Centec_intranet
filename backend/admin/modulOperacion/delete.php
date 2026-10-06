<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  if(isset($id))
  {
    enviar("UPDATE tahorro_motivo SET estado='2' WHERE idam='$id'");
  }
 ?>
