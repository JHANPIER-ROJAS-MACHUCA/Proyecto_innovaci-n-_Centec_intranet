<?php
require_once ("conection/bdcredito.php");
?>
<?php
$idU = $_REQUEST['txtidusuario'];
$contra = md5($_REQUEST['txtcontra']);
$contra1 = md5($_REQUEST['txtcontra1']);
$contra2 = md5($_REQUEST['txtcontra2']);
if ($contra1 == $contra2)
	{	
		  $U=extraer("(select * from tusuario where idU='$idU')");
  			$data =mysqli_fetch_array($U);		
		if ($contra == $data['pass']) 
		{
			
			enviar("Update tusuario Set pass='$contra1' where idU=$idU");
			?>
		<div class="alert alert-info" role="alert">
			<strong>Contraseña Cambiada Correctamente!!!</strong>
		</div>
		<?php
		/*
		 echo "<div align='center' id='loader' class='text-center'> <img src='ajax-loader.gif'></div>";
		echo " <META HTTP-EQUIV=Refresh CONTENT=1;URL=../index.php>";
		*/
		}
		else
		{
			?>
		<div class="alert alert-danger" role="alert">
			<strong>ERROR, La contraseña  no coincide!!!</strong>
		</div>
		<?php
		/*
		 echo "<div align='center' id='loader' class='text-center'> <img src='ajax-loader.gif'></div>";
		echo " <META HTTP-EQUIV=Refresh CONTENT=1;URL=principal_p.php>";
		*/
		}
	}
	else
	{
		?>
		<div class="alert alert-danger" role="alert">
			<strong>La contraseña no son iguales!!!</strong>
		</div>
		<?php
		/*
		 echo "<div align='center' id='loader' class='text-center'> <img src='ajax-loader.gif'></div>";
		echo " <META HTTP-EQUIV=Refresh CONTENT=1;URL=principal_p.php>";
		*/
	}
//echo "<span id='success'>Modificado satisfactoriamente...!!</span><br/>";
?>
