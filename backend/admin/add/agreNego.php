<?php
  include('../conection/bdcredito.php');
  extract($_POST);

  if(isset($ide) && isset($dire)  && isset($tipo) && isset($negocio) && isset($local) && isset($tiempo))
  {
    enviar("INSERT INTO tclie_negocio (idCG, direccion, tipo, tipoLocal, tipoNegocio, tiempo) values ('$ide','$dire','$tipo','$local','$negocio','$tiempo')");
  }

 ?>
