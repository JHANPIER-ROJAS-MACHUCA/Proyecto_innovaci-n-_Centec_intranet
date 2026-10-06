<?php
include('../conection/bdcredito.php');
extract($_POST);
if(isset($defini) && isset($_COOKIE['user1']) && isset($_COOKIE['tofi']))
{
  $info=extraer("SELECT monto as dinero FROM tcaja_oficina where idCO='$defini'");
  $regi=mysqli_fetch_array($info);
  $descu=$regi['dinero'];

  //$dife=extraer("SELECT sum(monto) as resta FROM tcaja_ofi_deta where idCO='$defini'");
  $dife=extraer("SELECT sum(tca.monto) as resta FROM tcaja_oficina tco inner join tcaja_usuario tcu on tco.idCO=tcu.idCO inner join tcaja_usu_detal tca on tcu.idCA=tca.idCA  where tco.idCO='$defini'");
  $decodificando=mysqli_fetch_array($dife);
  $saldo=$decodificando['resta'];
  echo ($descu-$saldo);
}
?>
