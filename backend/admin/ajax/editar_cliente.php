<?php
	//include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/*Inicia validacion del lado del servidor*/
	if (empty($_POST['mod_id'])) {
           $errors[] = "ID vacío";
        }else if (empty($_POST['mod_dni'])) {
           $errors[] = "Dni vacío";
 
		} else if (empty($_POST['mod_celular'])){
			$errors[] = "Celular vacío";
		} else if (
			!empty($_POST['mod_id']) &&
			!empty($_POST['mod_dni']) &&
	
			!empty($_POST['mod_celular'])
		){
		/* Connect To Database*/
		require_once ("../conection/db.php");//Contiene las variables de configuracion para conectar a la base de datos
		require_once ("../conection/conexion2.php");//Contiene funcion que conecta a la base de datos
		// escaping, additionally removing everything that could be (html/javascript-) code
		$ap=mysqli_real_escape_string($con,(strip_tags($_POST["mod_ap"],ENT_QUOTES)));
		$dni=mysqli_real_escape_string($con,(strip_tags($_POST["mod_dni"],ENT_QUOTES)));
		$correo=mysqli_real_escape_string($con,(strip_tags($_POST["mod_correo"],ENT_QUOTES)));
		$nhijos=mysqli_real_escape_string($con,(strip_tags($_POST["mod_nhijos"],ENT_QUOTES)));
		$ecivil=mysqli_real_escape_string($con,(strip_tags($_POST["mod_ecivil"],ENT_QUOTES)));
		$referencias=mysqli_real_escape_string($con,(strip_tags($_POST["mod_referencias"],ENT_QUOTES)));
		$am=mysqli_real_escape_string($con,(strip_tags($_POST["mod_am"],ENT_QUOTES)));
		$sexo=mysqli_real_escape_string($con,(strip_tags($_POST["mod_sexo"],ENT_QUOTES)));
		$rubro=mysqli_real_escape_string($con,(strip_tags($_POST["mod_rubro"],ENT_QUOTES)));
		$grado=mysqli_real_escape_string($con,(strip_tags($_POST["mod_grado"],ENT_QUOTES)));
		$lugarnac=mysqli_real_escape_string($con,(strip_tags($_POST["mod_lugarnac"],ENT_QUOTES)));
		$tipo=mysqli_real_escape_string($con,(strip_tags($_POST["mod_tipo"],ENT_QUOTES)));
		$nombres=mysqli_real_escape_string($con,(strip_tags($_POST["mod_nombres"],ENT_QUOTES)));
		$telefono=mysqli_real_escape_string($con,(strip_tags($_POST["mod_telefono"],ENT_QUOTES)));
		$celular=mysqli_real_escape_string($con,(strip_tags($_POST["mod_celular"],ENT_QUOTES)));
		$fecha=mysqli_real_escape_string($con,(strip_tags($_POST["mod_fecha"],ENT_QUOTES)));
		$direccion=mysqli_real_escape_string($con,(strip_tags($_POST["mod_direccion"],ENT_QUOTES)));
		$comentario=mysqli_real_escape_string($con,(strip_tags($_POST["mod_comentario"],ENT_QUOTES)));
		$idU=mysqli_real_escape_string($con,(strip_tags($_POST["mod_idusu"],ENT_QUOTES)));

		$risk_profile_id = isset($_POST['risk_profile_id']) && !empty($_POST['risk_profile_id']) ? $_POST['risk_profile_id'] : null;
		$client_type = isset($_POST['client_type']) && !empty($_POST['client_type']) ? $_POST['client_type'] : null;
		$status = (isset($_POST['status']) && !empty($_POST['status'])) ? $_POST['status'] : 'ACTIVE';

			$sqlusu="SELECT * FROM  tusuario where idU='$idU'";
		$queryusu = mysqli_query($con, $sqlusu);
		$rowusu=mysqli_fetch_array($queryusu);
		$idO=$rowusu['idO'];
		//$precio_ingreso=floatval($_POST['mod_precio']);
		$id_cliente=$_POST['mod_id'];
		$sql="UPDATE tclie_general SET ap='".$ap."',dni='".$dni."',correo='".$correo."', n_hijos='".$nhijos."', estado_civil='".$ecivil."', referencia='".$referencias."', am='".$am."', sexo='".$sexo."', grado_inst='".$grado."',rubro='".$rubro."', lugar_nac='".$lugarnac."', tipo='".$tipo."', nom='".$nombres."', telefono='".$telefono."', cel='".$celular."', fec_nac='".$fecha."', direc='".$direccion."', comentario='".$comentario."',idU='".$idU."',idO='".$idO."' ,risk_profile_id='".$risk_profile_id."' ,client_type='".$client_type."' ,status='".$status."' WHERE idCG='".$id_cliente."'";
		$query_update = mysqli_query($con,$sql);
			if ($query_update){
				$messages[] = "Cliente ha sido actualizado satisfactoriamente.";
			} else{
				$errors []= "Lo siento algo ha salido mal intenta nuevamente.".mysqli_error($con);
			}
		} else {
			$errors []= "Error desconocido.";
		}
		
		if (isset($errors)){
			
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
			if (isset($messages)){
				
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