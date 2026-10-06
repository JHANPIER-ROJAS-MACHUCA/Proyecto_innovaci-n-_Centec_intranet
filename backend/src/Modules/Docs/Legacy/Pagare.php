<?php

namespace CrediSoporte\Modules\Docs\Legacy;

use Dompdf\Dompdf;

// Logica migrada desde app/pdf/pagare.php
class Pagare
{
    public static function handle()
    {
        global $database;
ob_start();
include \CrediSoporte\Core\Bootstrap::root() . '/src/Modules/Docs/Legacy/content/pagareContent.php';
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
    }
}
