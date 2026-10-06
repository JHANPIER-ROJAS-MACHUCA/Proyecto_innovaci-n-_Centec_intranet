<?php
	  if(!isset($_COOKIE['user1']))
  {
   //echo "<script>location.href='../'</script>";
  }

	include("../../funciones.php");
	//usuario que atendio
  $datosU=$_GET['usu'];
	//cliente
  $cliente=$_GET['clie'];
	$dire=$_GET['dire'];
	$tel=$_GET['tel'];
	//correo de la oficina
	$corre=$_GET['corre'];
	//nombre del operador
	$opera=$_GET['opera'];
	//total
	$total=$_GET['total'];
	//sub total
	$sub=$_GET['sub'];
	//cantidad de mora
	$mora=$_GET['mora'];

	//numero de cuotas pagadas
	$cuo=$_GET['cumon'];
	if($cuo=="")
	{
		$cuo="--";
	}
	//total
	$totalF=$_GET['totalF'];
	//pendien
	$pendi=$_GET['pendi'];
	//vencidas
	$venci=$_GET['vencid'];
	$S="S/.";

	require_once(dirname(__FILE__).'/../html2pdf.class.php');
    // get the HTML
 	ob_start();
  error_reporting(E_ALL & ~E_NOTICE);
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
  include(dirname('__FILE__').'/juve/cuerpo_Boucher.php');
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
        $html2pdf->Output('Boucher.pdf');
        //
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
