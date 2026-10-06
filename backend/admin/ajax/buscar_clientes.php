<?php
//include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
/* Connect To Database*/
require_once("../conection/db.php"); //Contiene las variables de configuracion para conectar a la base de datos
require_once("../conection/conexion2.php"); //Contiene funcion que conecta a la base de datos
//Archivo de funciones PHP

include("../funciones.php");
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';
if (isset($_GET['id'])) {

	$id_cliente = intval($_GET['id']);
	$query = mysqli_query($con, "select * from tprestamo where idCG='" . $id_cliente . "'");
	$count = mysqli_num_rows($query);
	if ($count == 0) {

		if ($delete1 = mysqli_query($con, "DELETE FROM tclie_general WHERE idCG='" . $id_cliente . "'")) {
?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<strong>Aviso!</strong> Datos eliminados exitosamente.
			</div>
		<?php
		} else {
		?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<strong>Error!</strong> Lo siento algo ha salido mal intenta nuevamente.
			</div>
		<?php

		}
	} else {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<strong>Error!</strong> No se pudo eliminar éste cliente. Existen prestamos
		</div>
	<?php
	}
}
if ($action == 'ajax') {
	// escaping, additionally removing everything that could be (html/javascript-) code

	$dictionaries = [];
	$stmtDictionaries = mysqli_query($con, "SELECT * FROM dictionaries WHERE type='RISK_PROFILE'");
	while ($dictionary = mysqli_fetch_array($stmtDictionaries)) {
		array_push($dictionaries, $dictionary);
	}


	$q = mysqli_real_escape_string($con, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
	$aColumns = array('dni', 'nom', 'ap'); //Columnas de busqueda
	$sTable = "tclie_general";
	$sWhere = "where idU is not null";
	if ($_GET['q'] != "") {
		$sWhere = " WHERE (";
		for ($i = 0; $i < count($aColumns); $i++) {
			$sWhere .= $aColumns[$i] . " LIKE '%" . $q . "%' OR ";
		}
		$sWhere = substr_replace($sWhere, "", -3);
		$sWhere .= ')';
	}
	$sWhere .= " order by idCG desc";
	include 'pagination.php'; //include pagination file
	//pagination variables
	$page = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
	$per_page = 10; //how much records you want to show
	$adjacents  = 4; //gap between pages after number of adjacents
	$offset = ($page - 1) * $per_page;
	//Count the total number of row in your table*/
	$count_query   = mysqli_query($con, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
	$row = mysqli_fetch_array($count_query);
	$numrows = $row['numrows'];
	$total_pages = ceil($numrows / $per_page);
	$reload = './clientes.php';
	//consulta
	$sql = "SELECT * FROM  $sTable $sWhere  LIMIT $offset,$per_page";
	$query = mysqli_query($con, $sql);
	//loop through fetched data
	if ($numrows > 0) {

	?>
		<div class="wrapper wrapper-content animated fadeInRight">
			<div class="row">
				<?php
				$cont = 0;
				while ($row = mysqli_fetch_array($query)) {
					$id_cliente = $row['idCG'];
					$codigo_cliente = $row['idCG'];
					$apeynom = $row['ap'] . " " . $row['am'] . " " . $row['nom'];
					$ap = $row['ap'];
					$dni = $row['dni'];
					$nhijos = $row['n_hijos'];
					$correo = $row['correo'];
					$ecivil = $row['estado_civil'];
					$referencias = $row['referencia'];
					$am = $row['am'];
					$sexo = $row['sexo'];
					$imgC = $row['imgC'];
					$grado = $row['grado_inst'];
					$lugarnac = $row['lugar_nac'];
					$tipo = $row['tipo'];
					$nom = $row['nom'];
					$telefono = $row['telefono'];
					$rubro = $row['rubro'];
					$celular = $row['cel'];
					$fecha = $row['fec_nac'];
					$direccion = $row['direc'];
					$comentario = $row['comentario'];
					$correo = $row['correo'];
					$idU = $row['idU'];

					$color = 'white';

					$a = array_search($row['risk_profile_id'], array_column($dictionaries, 'id'));
					$dictionaryDescription = $a != '' ? $dictionaries[$a]['description'] : $a;

					switch ($dictionaryDescription) {
						case 'red':
							$color = "#FFAB91";
							break;
						case 'yellow':
							$color = "#FFF59D";
							break;
						case 'gray':
							$color = "#B0BEC5";
							break;
						case 'green':
							$color = "#CCFF90";
							break;
					}

				?>

					<input type="hidden" value="<?php echo $codigo_cliente; ?>" id="codigo_cliente<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $ap; ?>" id="ap<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $dni; ?>" id="dni<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $correo; ?>" id="correo<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $nhijos; ?>" id="nhijos<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $ecivil; ?>" id="ecivil<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $referencias; ?>" id="referencias<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $am; ?>" id="am<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $sexo; ?>" id="sexo<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $grado; ?>" id="grado<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $lugarnac; ?>" id="lugarnac<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $tipo; ?>" id="tipo<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $nom; ?>" id="nom<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $telefono; ?>" id="telefono<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $rubro; ?>" id="rubro<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $celular; ?>" id="celular<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $fecha; ?>" id="fecha<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $direccion; ?>" id="direccion<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $comentario; ?>" id="comentario<?php echo $id_cliente; ?>">
					<input type="hidden" value="<?php echo $idU; ?>" id="idU<?php echo $id_cliente; ?>">
					
					<input type="hidden" value="<?php echo $row['risk_profile_id'] ?>" id="risk_profile_id<?php echo $id_cliente ?>">
					<input type="hidden" value="<?php echo $row['client_type'] ?>" id="client_type<?php echo $id_cliente ?>">
					<input type="hidden" value="<?php echo $row['status'] ?>" id="status<?php echo $id_cliente ?>">

					<?php
					$cont++;
					if ($cont == 4) {
					?>
						<br>
					<?php
						$cont = 0;
					} ?>
					<div class="col-lg-3">
						<div class="contact-box center-version">
							<a href="" style="height:340px; background-color: <?php echo $color ?>">

								<?php
								if ($imgC == "") {

									if ($sexo == "M") {
										# code...
								?>
										<img alt="image" class="img-circle" src="img/profile.jpg">
									<?php
									} else if ($sexo == "F") {
										# code...
									?>
										<img alt="image" class="img-circle" src="img/profile2.jpg">
									<?php
									} else {
										# code...
									?>
										<img alt="image" class="img-circle" src="img/profile3.jpg">
								<?php
									}
								} else {

									echo "<img  alt='image' class='img-circle' src='img2/clie/$row[imgC]' >";
								}
								?>

								<h3 class="m-b-xs"><strong><?php echo $apeynom; ?></strong></h3>
								<div class="font-bold"><i class="fa fa-address-card-o"> </i> <?php echo $dni; ?></div>
								<div><span style="font-weight: bold;">CODIGO:</span> <?php echo str_pad($codigo_cliente,5,'0', STR_PAD_LEFT) ?></div>
								<div>
									<b>PERFIL DE RIESGO:</b>
									<?php
									switch ($dictionaryDescription) {
										case 'red':
											echo 'Alto riesgo';
											break;
										case 'yellow':
											echo 'Mediano riesgo';
											break;
										case 'gray':
											echo 'No registra información en el mes';
											break;
										case 'green':
											echo 'Sin riesgo';
											break;
										
										default:
											break;
									}
									?>
								</div>
								<address class="m-t-md">
									<strong>Datos:</strong><br>
									<abbr title="Direccion"><i class="fa fa-map-marker"></i> </abbr><?php echo $direccion; ?><br>
									<abbr title="Correo"><i class="fa fa-envelope-o"> </i> </abbr> <?php echo $correo; ?><br>
									<abbr title="Telefono"><i class="fa fa-fax"> </i> </abbr><?php echo $telefono; ?><br>
									<abbr title="Celular"><i class="fa fa-phone"> </i> </abbr> <?php echo $celular; ?>
								</address>

							</a>
							<div class="contact-box-footer">
								<div class="m-t-xs btn-group">
									<?php
									if ($_COOKIE['tuser'] != '4') { ?>
										<form action="perfilCliente.php" method="post">
											<input type="hidden" name="idCG" value="<?php echo $id_cliente; ?>">
											<button type="submit" class="btn btn-xs btn-white"><i class="fa fa-linode"></i> Data</button>
										</form>
									<?php } ?>
									<a href="tel:<?php echo $celular ?>" class="btn btn-xs btn-white"><i class="fa fa-phone"></i> Call </a>
									<a href="mailto:<?php echo $correo ?>" class="btn btn-xs btn-white"><i class="fa fa-envelope"></i> Email</a>

									<a href="#" class='btn btn-xs btn-default' title='Editar cliente' onclick="obtener_datos('<?php echo $id_cliente; ?>');" data-toggle="modal" data-target="#myModal2"><i class="glyphicon glyphicon-edit"></i> Editar</a>
									<a href="#" class='btn btn-xs btn-default' title='Borrar cliente' onclick="eliminar('<?php echo $id_cliente; ?>')"><i class="glyphicon glyphicon-trash"></i></a>
								</div>
							</div>
						</div>
					</div>
				<?php
				}
				?>
			</div>
		</div>
		<td colspan=6><span class="pull-right">
				<?php
				echo paginate($reload, $page, $total_pages, $adjacents);
				?> </span></td>
<?php
	}
}
?>