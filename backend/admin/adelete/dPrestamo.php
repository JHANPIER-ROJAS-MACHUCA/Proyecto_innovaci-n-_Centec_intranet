<?php
require_once ("../conection/bdcredito.php");
date_default_timezone_set('america/lima');      
$date_added=date("Y-m-d");
extract($_POST);
if(isset($id))
{
	$DetP=extraer("SELECT * FROM tpresta_detalle where idP='$id'");
	$countDp=mysqli_num_rows($DetP);
	if ($countDp==0)
	{
	echo "<script>alert('Prestamo ya agregado')</script>";
	echo "<script>window.close();</script>";
	exit;
	}
enviar("UPDATE tprestamo SET  fechaDesembolso='$date_added' where idP='$id'");
$Prestamo=extraer("SELECT * FROM tprestamo where  idP='$id'");
$dataP=mysqli_fetch_array($Prestamo);
$fecha=$dataP['fechaDesembolso'];
$tipo=$dataP['tipoP'];
$tiempo=$dataP['plazo'];
$cuota=$dataP['cuota'];

 //$identiUsuario=$_COOKIE['user1'];
 //  enviar("INSERT INTO tcaja_usu_detal (idCA,idR,monto,tipo,habilitacion) values ('$selecionador','$identiUsuario','$monto','2','1')");
$f= date("d-m-Y ");
  $f=str_replace("-","/",$f);
		  //echo "$fecha.<br>";
		  //echo $fecha;
		  switch ($tipo) {
		    case '1':
		          $com="1 days";
		          break;
		    case '2':
		          $com="1 week";
		          break;
		    case '3':
		          $com="2 week";
		          break;
		    case '4':
		          $com="1 days";
		      break;
		  }
		  $cont=0;
		  for ($i=0; $i < $tiempo; $i++)
		  {
		      $fecha=date("d-m-Y",strtotime($fecha."+".$com));

		      $dia=date("w", strtotime($fecha));

		      if($dia=='0')
		      {
		        $i--;
		      }
		      else
		      {
		        if(feriados($fecha)=='1')
		        {
		          $i--;
		        }
		        else
		        {
		          //echo "<br>".$fecha;
		          $cont++;
		           enviar("INSERT INTO tpresta_detalle (idP,fechaProg,ncutota,cuota) VALUES ('$id','$fecha','$cont','$cuota')");
		        }
		      }
		  }
		
}		

function feriados($fec)
		{
		  $resul='0';
		  $fec1 = preg_split("~-~", $fec);
		  $fec="$fec1[0]/$fec1[1]";
		  $info=extraer("SELECT COUNT(*) AS con from tfecha where fecha='$fec'");
		  while($row =mysqli_fetch_array($info))
		  {
		    $dato=$row['con'];
		  }
		  if(!empty($dato))
		  {
		    $resul='1';
		  }
		  return $resul;
		}
		 ?>

