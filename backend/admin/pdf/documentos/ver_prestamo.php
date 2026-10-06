<?php
if (!isset($_COOKIE['user1'])) {
	echo "<script>location.href='../'</script>";
}
if (empty($_COOKIE['tofi'])) {
	$idOfi = "1";
} else {
	$idOfi = $_COOKIE['tofi'];
}

/* Connect To Database*/
include("../../conection/db.php");
include("../../conection/conexion2.php");
//Archivo de funciones PHP
include("../../funciones.php");
//=======datosd de empresas
$jua = mysqli_query($con, "SELECT id, logo, titulo, nombreEmpresa, siglas,comentario, subnombre, color, ico FROM tdatos limit 1");
$jua1 = mysqli_fetch_array($jua);
$comentaJuve = $jua1['comentario'];
//=============================

$idprestamo = intval($_GET['idprestamo']);
$sql_count = mysqli_query($con, "select * from tprestamo where idP='" . $idprestamo . "'");
$count = mysqli_num_rows($sql_count);
if ($count == 0) {
	echo "<script>alert('Prestamo no encontrada')</script>";
	echo "<script>window.close();</script>";
	exit;
}
$sql_prestamo = mysqli_query($con, "select * from tprestamo p where  p.idP='" . $idprestamo . "'");
$rw_prestamo = mysqli_fetch_array($sql_prestamo);
$idPrest = $rw_prestamo['idP'];
$fechaD = $rw_prestamo['fechaDesembolso'];
$producto = $rw_prestamo['tipoP'];
$tipo = $rw_prestamo['pago'];
$montoA = $rw_prestamo['montoAprovado'];
$id_cliente = $rw_prestamo['idCG'];
$taza = $rw_prestamo['taza'];
$ncredito = $rw_prestamo['n_credito'];
$moraP = $rw_prestamo['mora'];
$S = "S/.";
$plazo = $rw_prestamo['plazo'];
$codigo_credito = $rw_prestamo['idP'];
$totalACancelar = $montoA + ($montoA * $taza / 100);

$limitadorPrestamo = $rw_prestamo['n_cuota'];

$sql_cliente = mysqli_query($con, "SELECT 
concat(clie.nom, ' ', clie.ap, ' ', clie.am) AS cliente, 
clie.cel, clie.direc, clie.referencia, 
concat(usu.nomU, ' ', usu.apU, ' ', usu.amU) AS usuario,
usu.celU AS usuario_celular
FROM tclie_general AS clie 
INNER JOIN tusuario AS usu ON clie.idU=usu.idU 
WHERE clie.idCG='$id_cliente'");
$rw_cliente = mysqli_fetch_array($sql_cliente);

$analista_nombre = $rw_cliente['usuario'];
$analista_celular = $rw_cliente['usuario_celular'];

$conejo = "font-size:7px";
//$conejo1="height:6px";
if ((int)$limitadorPrestamo > 35) {
	$conejo = "font-size:6px";
}
require_once(dirname(__FILE__) . '/../html2pdf.class.php');
// get the HTML
ob_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
include(dirname('__FILE__') . '/res/ver_prestamo_html.php');
$content = ob_get_clean();
try {
	// init HTML2PDF
	$html2pdf = new HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(0, 0, 0, 0));
	// display the full page
	$html2pdf->pdf->SetDisplayMode('fullpage');
	// convert
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	// send the PDF
	$html2pdf->Output('Prestamo.pdf');
	//
} catch (HTML2PDF_exception $e) {
	echo $e;
	exit;
}
