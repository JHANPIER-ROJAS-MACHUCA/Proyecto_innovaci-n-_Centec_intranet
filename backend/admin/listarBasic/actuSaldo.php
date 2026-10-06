<?php
  include('../conection/bdcredito.php');
  extract($_POST);
  if( isset($_COOKIE['user1']) && isset($_COOKIE['tofi']))
  {
    $identiUsuario=$_COOKIE['user1'];
    $identiOficina=$_COOKIE['tofi'];
    //obtener el saldo del usuario que se desgino por la caja
    $obt=extraer("SELECT sum(tca.monto) as monto FROM tcaja_oficina tco inner join tcaja_usuario tcu on tco.idCO=tcu.idCO inner join tcaja_usu_detal tca on tcu.idCA=tca.idCA inner join tusuario tus on tcu.idU=tus.idU where tco.idO='$identiOficina' and tcu.idU='$identiUsuario' and tco.fin is null and tca.habilitacion='4' order by tca.idCAD desc");
    $row=mysqli_fetch_array($obt);
    echo $row['monto'];
  }
?>
