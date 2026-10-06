<?php
include('../../../conection/db7.php');

extract($_POST);

enviar("DELETE FROM tbilletaje where idBille='$codigo'");
 ?>
