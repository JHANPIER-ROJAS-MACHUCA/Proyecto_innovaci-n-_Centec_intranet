<?php
include('../../../conection/db7.php');

extract($_POST);

enviar("UPDATE tbilletaje SET estado='1' where idBille='$codigo'");
 ?>
