<?php
require_once ("conection/bdcredito.php");
?>
<?php
$correo=$_POST["txtcorreo"];
$direc=$_POST["txtdireccion"];
$cel=$_POST["txtcelular"];
$idU=$_POST["txtidusuario"];
enviar("UPDATE tusuario SET  celU='$cel',direcU='$direc',correoU='$correo' where idU='$idU'");
//echo "<span id='success'>Modificado satisfactoriamente...!!</span><br/>";
?>
 <div class="alert alert-info">
                Modificado correctamente.
            </div>