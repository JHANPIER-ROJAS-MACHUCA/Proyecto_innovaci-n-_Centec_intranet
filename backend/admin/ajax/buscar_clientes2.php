<?php
	//include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/* Connect To Database*/
	require_once ("../conection/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../conection/conexion2.php");//Contiene funcion que conecta a la base de datos
	//Archivo de funciones PHP
	include("../funciones.php");
	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
	if (isset($_GET['id'])){
		$id_cliente=intval($_GET['id']);
		
	
			if ($delete1=mysqli_query($con,"DELETE FROM tclie_general WHERE idCG='".$id_cliente."'")){
			?>
			<div class="alert alert-success alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <strong>Aviso!</strong> Datos eliminados exitosamente.
			</div>
			<?php 
		}else {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <strong>Error!</strong> Lo siento algo ha salido mal intenta nuevamente.
			</div>
			<?php
			
		}
	}
	if($action == 'ajax'){
		// escaping, additionally removing everything that could be (html/javascript-) code
		
         $q = mysqli_real_escape_string($con,(strip_tags($_REQUEST['q'], ENT_QUOTES)));
		 $aColumns = array('dni', 'nom');//Columnas de busqueda
		 $sTable = "tclie_general";
		 $sWhere = "";
		if ( $_GET['q'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$sWhere .= $aColumns[$i]." LIKE '%".$q."%' OR ";
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		$sWhere.=" order by idCG desc";
		include 'pagination.php'; //include pagination file
		//pagination variables
		$page = (isset($_REQUEST['page']) && !empty($_REQUEST['page']))?$_REQUEST['page']:1;
		$per_page = 10; //how much records you want to show
		$adjacents  = 4; //gap between pages after number of adjacents
		$offset = ($page - 1) * $per_page;
		//Count the total number of row in your table*/
		$count_query   = mysqli_query($con, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
		$row= mysqli_fetch_array($count_query);
		$numrows = $row['numrows'];
		$total_pages = ceil($numrows/$per_page);
		$reload = './clientes.php';
		//main query to fetch the data
		$sql="SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page";
		$query = mysqli_query($con, $sql);
		//loop through fetched data
		if ($numrows>0){
			
			?>
			<div class="table-responsive">
			  <table class="table">
				<tr  class="info">
					<th>Código</th>
					<th>Dni</th>
					<th>Apellidos Y Nombres</th>
					<th>Telefono</th>
					<th>Celular</th>
					<th class='text-right'>Acciones</th>
					
				</tr>
				<?php
				while ($row=mysqli_fetch_array($query)){
						$id_cliente=$row['idCG'];
						$codigo_cliente=$row['idCG'];
						$apeynom=$row['ap']."  ".$row['nom'];
						$ap=$row['ap'];
						$dni=$row['dni'];
						$nhijos=$row['n_hijos'];
						$ecivil=$row['estado_civil'];
						$referencias=$row['referencia'];
						$am=$row['am'];
						$sexo=$row['sexo'];
						$grado=$row['grado_inst'];
						$lugarnac=$row['lugar_nac'];
						$tipo=$row['tipo'];
						$nom=$row['nom'];
						$telefono=$row['telefono'];
						$celular=$row['cel'];
						$fecha=$row['fec_nac'];
						$direccion=$row['direc'];
						$comentario=$row['comentario'];
					?>
				
					<input type="hidden" value="<?php echo $codigo_cliente;?>" id="codigo_cliente<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $ap;?>" id="ap<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $dni;?>" id="dni<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $nhijos;?>" id="nhijos<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $ecivil;?>" id="ecivil<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $referencias;?>" id="referencias<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $am;?>" id="am<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $sexo;?>" id="sexo<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $grado;?>" id="grado<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $lugarnac;?>" id="lugarnac<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $tipo;?>" id="tipo<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $nom;?>" id="nom<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $telefono;?>" id="telefono<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $celular;?>" id="celular<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $fecha;?>" id="fecha<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $direccion;?>" id="direccion<?php echo $id_cliente;?>">
					<input type="hidden" value="<?php echo $comentario;?>" id="comentario<?php echo $id_cliente;?>">
				
					<tr>
						
						<td><?php echo $codigo_cliente; ?></td>
						<td ><?php echo $dni; ?></td>
						<td ><?php echo $apeynom; ?></td>
						<td><?php echo $telefono;?></td>
						<td><?php echo $celular;?></td>
					
					<td ><span class="pull-right">
						
					<a href="#" class='btn btn-default' title='Editar cliente' onclick="obtener_datos('<?php echo $id_cliente;?>');" data-toggle="modal" data-target="#myModal2"><i class="glyphicon glyphicon-edit"></i></a> 

    		<a href="#" class='btn btn-default' title='Borrar cliente' onclick="eliminar('<?php echo $id_cliente; ?>')"><i class="glyphicon glyphicon-trash"></i></a>
                            <?php
                      
					?>
				</span></td>
						
					</tr>
					<?php
				}
				?>
				<tr>
					<td colspan=6><span class="pull-right">
					<?php
					 echo paginate($reload, $page, $total_pages, $adjacents);
					?></span></td>
				</tr>
			  </table>
			</div>
			<?php
		}
	}
?>