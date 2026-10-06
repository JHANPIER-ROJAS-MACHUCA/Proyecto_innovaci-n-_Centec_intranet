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

	$ide=($_GET['idCliente']);
	$ident=preg_split("~j~",$ide);
	$id_cliente=$ident['0'];
	$canasta=$ident['1'];

	require_once(dirname(__FILE__).'/../html2pdf.class.php');
    // get the HTML
 		   ob_start();
  error_reporting(E_ALL & ~E_NOTICE);
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
     include(dirname('__FILE__').'/motivo/ver_motivo.php');
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
        $html2pdf->Output('Motivo.pdf');
        //
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
