<?php

namespace CrediSoporte\Modules\Attachments\Controllers;

use CrediSoporte\Domain\Models\Attachment;
use CrediSoporte\Domain\Request\Request;
use CrediSoporte\Domain\Session\Session;

// Logica migrada desde app/Controllers/DeleteAttachmentController.php
class DeleteAttachmentController
{
    public static function handle(): void
    {
session_start();

$request = new Request();
$session = new Session();

$attachment = Attachment::find($request->get('attachmentId'));

if (!is_null($attachment)) {
    if (file_exists(\CrediSoporte\Core\Bootstrap::root() . '/storage/attachments/'. $attachment->name)) {
        unlink(\CrediSoporte\Core\Bootstrap::root() . '/storage/attachments/' . $attachment->name);
    }
    
    $attachment->delete();
    
    $session->setFlash('message', 'Archivo eliminado con exito!!');
}else{
    $session->setFlash('message', 'El archivo a eliminar no existe.');
}

header('Location:' . \CrediSoporte\Core\Bootstrap::root() . '/admin/formatos.php');
    }
}
