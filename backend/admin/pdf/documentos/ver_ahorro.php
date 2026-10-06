<?php
	  if(!isset($_COOKIE['user1']))
  {
   echo "<script>location.href='../'</script>";
  }
	/* Connect To Database*/
	include("../../conection/db.php");
	include("../../conection/conexion2.php");
	//Archivo de funciones PHP
	include("../../funciones.php");
	$idahorro= intval($_GET['idahorro']);		
	$sql_count=mysqli_query($con,"select * from tahorro where idA='".$idahorro."'");
	$count=mysqli_num_rows($sql_count);
	if ($count==0)
	{
	echo "<script>alert('Ahorro no encontrado')</script>";
	echo "<script>window.close();</script>";
	exit;
	}
	$sql_a=mysqli_query($con,"select * from tahorro a where  a.idA='".$idahorro."'");
	$rw_a=mysqli_fetch_array($sql_a);
	$idAhor=$rw_a['idA'];
	$id_cliente=$rw_a['id'];
	$tipo=$rw_a['tipoA'];
	$S="S/.";
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
    // get the HTML
 		   ob_start();
  error_reporting(E_ALL & ~E_NOTICE);
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
     include(dirname('__FILE__').'/res/ver_ahorro_html.php');
    $content = ob_get_clean();
    try
    {
        // init HTML2PDF
        $html2pdf = new HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(0, 0, 0, 0));
        // display the full page
        $html2pdf->pdf->SetDisplayMode('fullpage');
        // convert
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        // send the PDF
        $html2pdf->Output('Ahorro.pdf');
        //
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
 
