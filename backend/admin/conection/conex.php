<?php
/*$hostname_conex = "167.114.218.76";
$database_conex = "cent3cpcom_juve";
$username_conex = "cent3cpcom_juve7";
$password_conex = "@freedom7@";*/

$hostname_conex = "localhost";
$database_conex = "credisoportecom_credisopo";
$username_conex = "credisoportecom_credisopo";
$password_conex = "VW]xmCV*6ktd";
// creación de la conexión a la base de datos con mysql_connect()
$conex = mysqli_connect($hostname_conex, $username_conex, $password_conex, $database_conex) or
die ("No se ha podido conectar al servidor de Base de datos");
mysqli_set_charset($conex, 'UTF8');

?>
