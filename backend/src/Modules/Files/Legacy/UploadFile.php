<?php

namespace CrediSoporte\Modules\Files\Legacy;


// Logica migrada verbatim desde app/api/uploadFile.php.
// El wrapper en app/api/uploadFile.php preserva URL, entradas y salida legacy.
class UploadFile
{
    public static function handle(): void
    {$dotenv = Dotenv\Dotenv::createImmutable(\CrediSoporte\Core\Bootstrap::root());
$dotenv->load();

$directorio = \CrediSoporte\Core\Bootstrap::root() . '/storage/uploads/';
$fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
$fileName = time() . '.' . $fileExtension;
$subir_archivo = $directorio . $fileName;
if (move_uploaded_file($_FILES['file']['tmp_name'], $subir_archivo)) {
    $tmp = $subir_archivo;
    $info = exif_imagetype($tmp);
    switch ($info) {
        case IMAGETYPE_PNG:
            $original = imagecreatefrompng($tmp);
            break;
        case IMAGETYPE_JPEG:
            $original = imagecreatefromjpeg($tmp);
            break;
        case IMAGETYPE_GIF:
            $original = imagecreatefromgif($tmp);
            break;
        case IMAGETYPE_WEBP:
            $original = imagecreatefromwebp($tmp);
            break;
        case IMAGETYPE_BMP:
            $original = imagecreatefrombmp($tmp);
            break;
        case IMAGETYPE_AVIF:
            $original = imagecreatefromavif($tmp);
            break;
        default:
            die('Formato no soportado, lo siento');
    }
   
    if ($_FILES['file']['size'] > 20000) { 
        $original = imagescale($original, 900);
    } 

    $nombre = uniqid();
    $ruta_copia2 = "$directorio/$nombre.webp";
    if (imagewebp($original, $ruta_copia2, 60)) { 
        unlink($tmp);
        echo json_encode([
            'success' => true,
            'data' => [
                'path' => $_ENV['APP_URL'] . '/storage/uploads/',
                'name' => "$nombre.webp", 
                'name_original' => $_FILES['file']['name'],
                'extension' => 'webp', 
                'size' => filesize($ruta_copia2) 
            ]
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar la imagen optimizada',
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'La subida ha fallado',
    ]);
}
    }
}
