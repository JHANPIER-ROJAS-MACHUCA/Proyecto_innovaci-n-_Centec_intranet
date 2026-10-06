<?php
date_default_timezone_set('america/lima');
function conectar()
	{
		error_reporting(E_ERROR|E_WARNING|E_PARSE);
		/*$host="167.114.218.76";
		$db="cent3cpcom_juve";
		$usuario="cent3cpcom_juve7";
		$pass="@freedom7@";*/

		$host = "localhost";
		$db = "credisoportecom_credisopo";
		$usuario = "credisoportecom_credisopo";
		$pass  = "VW]xmCV*6ktd";

		$conexion = mysqli_connect($host,$usuario,$pass,$db) or die(mysqli_error($conexion));
		mysqli_set_charset($conexion, 'UTF8');
		return $conexion;
	}
function enviar($cadena)
	{
		mysqli_query(conectar(),$cadena);
		mysqli_close(conectar());
	}
function extraer($cadena)
	{
		$resul= (mysqli_query(conectar(),$cadena));
		return $resul;
	}
	function verifi($valor)
	{
		$resul=0;
		if($valor!='1' || $valor!='7')
		{
			$resul=1;
		}
		return $resul;
	}
	$jua=extraer("SELECT id, logo, titulo, nombreEmpresa, siglas,comentario, subnombre, color, ico FROM tdatos limit 1");
	$jua1=mysqli_fetch_array($jua);
	$comentaJuve=$jua1['comentario'];
?>
