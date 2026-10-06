<?php
require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Dompdf\Dompdf;

ob_start();
include dirname(__FILE__) . '/contrato_content.php';
$content = ob_get_clean();

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($content);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser

return $dompdf->stream("Archivo.php", array("Attachment" => false));
