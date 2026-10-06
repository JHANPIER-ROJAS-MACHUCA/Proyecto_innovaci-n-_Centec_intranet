<?php
include("../conection/bdcredito.php");
extract($_POST);


enviar("UPDATE tahorro_motivo_deta set domingo='$dom',lunes='$lu', martes='$ma', miercoles='$mi', jueves='$ju', viernes='$vi', sabado='$sab' where idamd='$id'");
 ?>
