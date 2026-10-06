<?php

	extract($_GET);

	$fechaD=$fecha;
	$tipo=$tipo;
	$montoA=$monto;

	$taza=$interes;
	$tipoPa=$tipoPago;
	$moraP=$mora;
	$cantidad=$cuotas;
	$ini=$pagoini;
	$fin=$pagofin;

	$S="S/.";
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
    // get the HTML
 		   ob_start();
  error_reporting(E_ALL & ~E_NOTICE);
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
     include(dirname('__FILE__').'/res/ver_simulador.php');
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
        $html2pdf->Output('Prestamo.pdf');
        //
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
