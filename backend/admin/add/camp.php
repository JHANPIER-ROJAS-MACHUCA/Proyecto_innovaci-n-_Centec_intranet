<?php
include("../conection/bdcredito.php");
extract($_POST);
$usuario = $_COOKIE['user1'];
if (isset($codi)) {
  // actualizamos el lugar de pago con un toggle
  // primero seleccionamos el estado actual
  // y lo cambiamos por el otro valor
  $result = extraer("SELECT tlocal FROM tprestamo WHERE idP='$codi'");
  $row = mysqli_fetch_array($result);

  $value = 1;
  if ($row['tlocal'] === '1') {
    $value = 2;
  }
  enviar("UPDATE tprestamo SET tlocal='$value' where idP='$codi'");
}
