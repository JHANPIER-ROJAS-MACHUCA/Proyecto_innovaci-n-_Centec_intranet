<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  if(isset($id))
  {
    enviar("DELETE FROM tcaja_usu_detal WHERE idCAD='$id'");
  }
 ?>
