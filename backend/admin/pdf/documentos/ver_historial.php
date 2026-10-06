<?php
	  if(!isset($_COOKIE['user1']))
  {
   echo "<script>location.href='../'</script>";
  }
	if(empty($_COOKIE['tofi']))
	{
		$idOfi="1";
	}
	else
	{
		$idOfi=$_COOKIE['tofi'];
	}
	/* Connect To Database*/
	include("../../conection/db.php");
	include("../../conection/conexion2.php");
	//Archivo de funciones PHP
	//==================empresa
	$jua=mysqli_query($con,"SELECT id, logo, titulo, nombreEmpresa, siglas,comentario, subnombre, color, ico FROM tdatos limit 1");
	$jua1=mysqli_fetch_array($jua);
	$comentaJuve=$jua1['comentario'];
	//=========================
	include("../../funciones.php");
	$idprestamo= intval($_GET['idprestamo']);
	$sql_count=mysqli_query($con,"select * from tprestamo where idP='".$idprestamo."'");
	$count=mysqli_num_rows($sql_count);
	if ($count==0)
	{
	echo "<script>alert('Prestamo no encontrada')</script>";
	echo "<script>window.close();</script>";
	exit;
	}
	$sql_prestamo=mysqli_query($con,"select * from tprestamo p where  p.idP='".$idprestamo."'");
	$rw_prestamo=mysqli_fetch_array($sql_prestamo);
	$idPrest=$rw_prestamo['idP'];
	$fechaD=$rw_prestamo['fechaDesembolso'];
	$tipo=$rw_prestamo['tipoP'];
	$montoA=$rw_prestamo['montoAprovado'];
	$id_cliente=$rw_prestamo['idCG'];
	$taza=$rw_prestamo['taza'];
	$ncredito=$rw_prestamo['n_credito'];
	$moraP=$rw_prestamo['mora'];
	$S="S/.";
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
    // get the HTML
 		   ob_start();
  error_reporting(E_ALL & ~E_NOTICE);
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
     include(dirname('__FILE__').'/res/ver_historial_html.php');
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
        $html2pdf->Output('Historial.pdf');
        //
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
