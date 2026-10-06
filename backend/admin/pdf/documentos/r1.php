<?php
	  if(!isset($_COOKIE['user1']))
  {
  // echo "<script>location.href='../'</script>";
  }
	/* Connect To Database*/
	include("../../conection/db.php");
	include("../../conection/conexion2.php");
	//Archivo de funciones PHP
/*	include("../../funciones.php");
	$idahorro= intval($_GET['idahorro']);
	$sql_count=mysqli_query($con,"select * from tahorro where idA='".$idahorro."'");
	$count=mysqli_num_rows($sql_count);
	if ($count==0)
	{
	echo "<script>alert('Ahorro no encontrado')</script>";
	echo "<script>window.close();</script>";
	exit;
	}
/*	$sql_a=mysqli_query($con,"select * from tahorro a where  a.idA='".$idahorro."'");
	$rw_a=mysqli_fetch_array($sql_a);
*/
$fr=preg_split("~/~",$_GET['codi']);
$idO=$fr[0];
$mesi=$fr[1];
$ini=$fr[2];
$fin=$fr[3];
//mes
switch ($mesi)
{
  case 1:
    $mes="Enero";
    break;
  case 2:
    $mes="Febrero";
    break;
  case 3:
    $mes="Marzo";
    break;
  case 4:
    $mes="Abril";
    break;
  case 5:
    $mes="Mayo";
    break;
  case 6:
    $mes="Junio";
    break;
  case 7:
    $mes="Julio";
    break;
  case 8:
    $mes="Agosto";
    break;
  case 9:
    $mes="Septiembre";
    break;
  case 10:
    $mes="Octubre";
    break;
  case 11:
    $mes="Noviembre";
    break;
  case 12:
    $mes="Diciembre";
    break;
	}
//cambio de tipo fecha
function fra($v)
{
  $f=preg_split("~-~",$v);
  $resul=$f[2]."/".$f[1]."/".$f[0];
  return $resul;
}
//===================
//recuperacion de mes
function fre($v)
{
  $f=preg_split("~-~",$v);
  $resul=$f[1];
  return $resul;
}
//===================

	require_once(dirname(__FILE__).'/../conejo.php');

	ob_start();
  error_reporting(E_ALL & ~E_NOTICE);
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
  include(dirname('__FILE__').'/res/r1.php');
  $content = ob_get_clean();
    try
    {
        // init HTML2PDF
        $html2pdf = new HTML2PDF('L', 'LETTER', 'es', true, 'UTF-8', array(0, 0, 0, 0));
        // display the full page
        $html2pdf->pdf->SetDisplayMode('fullpage');
        // convert
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        // send the PDF
        $html2pdf->Output('Reporte_Cierrre_mes.pdf');
        //
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
