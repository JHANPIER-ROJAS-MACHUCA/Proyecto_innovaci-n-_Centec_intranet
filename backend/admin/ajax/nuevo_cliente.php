<?php

if (empty($_POST['txtdni'])) {
	$errors[] = "Dni vacío";
} else if (empty($_POST['txtap'])) {
	$errors[] = "apellido vacío";
} else if (empty($_POST['ecivil'])) {
	$errors[] = "estado civil vacío";
} else if (empty($_POST['sexo'])) {
	$errors[] = "sexo vacío";
} else if (empty($_POST['tipo'])) {
	$errors[] = "tipo vacío";
} else if (
	!empty($_POST['txtdni']) &&
	!empty($_POST['txtap']) &&
	!empty($_POST['sexo']) &&
	!empty($_POST['tipo']) &&
	!empty($_POST['ecivil'])

) {
	/* Connect To Database*/
	require_once("../conection/db.php"); //Contiene las variables de configuracion para conectar a la base de datos
	require_once("../conection/conexion2.php"); //Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
	//$idofic= $_SESSION['idofic']  ;
	//$idu =$_SESSION['idusuario'];
	//	$sql_oficina=mysqli_query($con,"select * from oficina_usuario where idoficina='$idofic' and idusuario='$idu'");
	//$rw_oficina=mysqli_fetch_array($sql_oficina);
	//$idoficu = $rw_oficina['idoficina_u'];
	$ap = mysqli_real_escape_string($con, (strip_tags($_POST["txtap"], ENT_QUOTES)));
	$dni = mysqli_real_escape_string($con, (strip_tags($_POST["txtdni"], ENT_QUOTES)));
	$correo = mysqli_real_escape_string($con, (strip_tags($_POST["correo"], ENT_QUOTES)));
	$nhijos = mysqli_real_escape_string($con, (strip_tags($_POST["nhijos"], ENT_QUOTES)));
	$ecivil = mysqli_real_escape_string($con, (strip_tags($_POST["ecivil"], ENT_QUOTES)));
	$referencias = mysqli_real_escape_string($con, (strip_tags($_POST["referencias"], ENT_QUOTES)));
	$am = mysqli_real_escape_string($con, (strip_tags($_POST["txtam"], ENT_QUOTES)));
	$sexo = mysqli_real_escape_string($con, (strip_tags($_POST["sexo"], ENT_QUOTES)));
	$rubro = mysqli_real_escape_string($con, (strip_tags($_POST["rubro"], ENT_QUOTES)));
	$grado = mysqli_real_escape_string($con, (strip_tags($_POST["grado"], ENT_QUOTES)));
	$lugarnac = mysqli_real_escape_string($con, (strip_tags($_POST["lugarnac"], ENT_QUOTES)));
	$tipo = mysqli_real_escape_string($con, (strip_tags($_POST["tipo"], ENT_QUOTES)));
	$nombres = mysqli_real_escape_string($con, (strip_tags($_POST["txtnom"], ENT_QUOTES)));
	$telefono = mysqli_real_escape_string($con, (strip_tags($_POST["telefono"], ENT_QUOTES)));
	$celular = mysqli_real_escape_string($con, (strip_tags($_POST["celular"], ENT_QUOTES)));
	$fecha = mysqli_real_escape_string($con, (strip_tags($_POST["fecha"], ENT_QUOTES)));
	$direccion = mysqli_real_escape_string($con, (strip_tags($_POST["direccion"], ENT_QUOTES)));
	$comentario = mysqli_real_escape_string($con, (strip_tags($_POST["comentario"], ENT_QUOTES)));
	$idusu = mysqli_real_escape_string($con, (strip_tags($_POST["idusu"], ENT_QUOTES)));

	$risk_profile_id = $_POST['risk_profile_id'];
	$client_type = $_POST['client_type'];
	$status = $_POST['status'];

	//$precio_ingreso=floatval($_POST['precio']);
	//date_default_timezone_set('america/lima');      
	//$date_added=date("Y-m-d H:i:s");
	$sqlusu = "SELECT * FROM  tusuario where idU='$idusu'";
	$queryusu = mysqli_query($con, $sqlusu);
	$rowusu = mysqli_fetch_array($queryusu);
	$idO = $rowusu['idO'];

	$sql_count = mysqli_query($con, "select * from tclie_general where dni='" . $dni . "'");
	$count = mysqli_num_rows($sql_count);
	if ($count > 0) {
		echo "<script>alert('Cliente agregado')</script>";
		echo "<script>window.close();</script>";
		exit;
	}
	$sql = "INSERT INTO tclie_general (dni,direc,correo,telefono,rubro,cel,nom,ap,am,fec_nac,n_hijos,grado_inst,estado_civil,lugar_nac,referencia,tipo,comentario,distrito,provincia,tiempo_residencia,sexo,idU,idO,risk_profile_id,client_type,status) VALUES ('$dni','$direccion','$correo','$telefono','$rubro','$celular','$nombres','$ap','$am','$fecha','$nhijos','$grado','$ecivil','$lugarnac','$referencias','$tipo','$comentario','','','','$sexo','$idusu','$idO', '$risk_profile_id', '$client_type', '$status')";
	$query_new_insert = mysqli_query($con, $sql);
	if ($query_new_insert) {
		$messages[] = "cliente ha sido ingresado satisfactoriamente.";
	} else {
		$errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($con);
	}
} else {
	$errors[] = "Error desconocido.";
}

if (isset($errors)) {

?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Error!</strong>
		<?php
		foreach ($errors as $error) {
			echo $error;
		}
		?>
	</div>
<?php
}
if (isset($messages)) {

?>
	<div class="alert alert-success" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>¡Bien hecho!</strong>
		<?php
		foreach ($messages as $message) {
			echo $message;
		}
		?>
	</div>
<?php
}

?>