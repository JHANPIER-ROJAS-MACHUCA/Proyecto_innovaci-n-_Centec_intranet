<?php
require_once ("../conection/bdcredito.php");
//editar
extract($_POST);
if(!empty($idpe))
{
	$cosul="";
	enviar("UPDATE tprestamo SET montoPropuesto='$txtmontop', montoAprovado='$txtmontoa', cuota='$txtcuota', taza='$txttaza', fechaDesembolso='$txtfechad', pago='$txtpago', plazo='$txtplazo',n_credito='$txtncredito',n_cuota='$txtncuota',diasPasados='$txtdp',estado='$txtestado',fechaTermino='$txtfechat',tipoP='$txttip',montoDesembolso='$txtmontod' where idP='$idpe'");
}
else
{
	//agregar
	if (!empty($_POST['txtidCG'])
		&& !empty($_POST['txtmontop'])
		&& !empty($_POST['txtmontoa'])
		&& !empty($_POST['txtcuota'])
		&& !empty($_POST['txttaza'])
		&& !empty($_POST['txtfechad'])
		&& !empty($_POST['txtpago'])
		&& !empty($_POST['txtplazo'])
		&& !empty($_POST['txtncredito'])
		&& !empty($_POST['txtncuota'])
		&& !empty($_POST['txtdp'])
		&& !empty($_POST['txtestado'])
		&& !empty($_POST['txtfechat'])
		&& !empty($_POST['txttip'])
		&& !empty($_POST['txtmontod'])
		)
		{
			//session_start();
  //$idCG=$_SESSION['idCG']  ;
			//$idCG=$_POST['idCG'];

			enviar("INSERT INTO tprestamo(idCG, montoPropuesto, montoAprovado, cuota, taza, fechaDesembolso, pago, plazo, n_credito,n_cuota, diasPasados,estado,fechaTermino,tipoP,montoDesembolso)
						VALUES ('$txtidCG','$txtmontop','$txtmontoa','$txtcuota','$txttaza','$txtfechad','$txtpago','$txtplazo','$txtncredito','$txtncuota','$txtdp','$txtestado','$txtfechat','$txttip','$txtmontod')");
			enviar("INSERT INTO tvinculacion (titular) VALUES ('$txtidCG')");

			 $V=extraer("(select max(idV) as 'idmaxv' from tvinculacion)");
  				$maxV =mysqli_fetch_array($V);
  				$idVmax = $maxV['idmaxv'];

			  $P=extraer("(select max(idP) as 'idmaxp' from tprestamo)");
  				$maxP =mysqli_fetch_array($P);
  				$idPmax = $maxP['idmaxp'];
			enviar("UPDATE tprestamo SET idV='$idVmax' where idP='$idPmax'");

		}

	}
?>
