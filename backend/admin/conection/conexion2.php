<?php
    $con=@mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if(!$con){
        die("imposible conectarse: ".mysqli_error($con));
    }
    if (@mysqli_connect_errno()) {
        die("Conexión falló: ".mysqli_connect_errno()." : ". mysqli_connect_error());
    }
		$jua=mysqli_query($con,"SELECT id, logo, titulo, nombreEmpresa, siglas,comentario, subnombre, color, ico,abre,dnir,representante,direccion,partida,ruc FROM tdatos limit 1");
		$jua1=mysqli_fetch_array($jua);

?>
