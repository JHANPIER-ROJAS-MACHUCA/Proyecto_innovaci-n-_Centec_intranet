<?php
require_once ("../conection/bdcredito.php");
//date_default_timezone_set('america/lima');      
//$date_added=date("Y-m-d");
extract($_POST);
if(isset($id) && isset($monto))
{
   enviar("UPDATE tprestamo SET  montoAprovado='$monto',estado='2' where idP='$id'");
/*$Prestamo=extraer("SELECT * FROM tprestamo where  idP='$id'");
$dataP=mysqli_fetch_array($Prestamo);
$fechaD=$dataP['fechaDesembolso'];
$tipo=$dataP['tipoP'];
*/
 //$identiUsuario=$_COOKIE['user1'];
 //  enviar("INSERT INTO tcaja_usu_detal (idCA,idR,monto,tipo,habilitacion) values ('$selecionador','$identiUsuario','$monto','2','1')");
}

 ?>
