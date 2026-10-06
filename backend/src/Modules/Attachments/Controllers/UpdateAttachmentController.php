<?php

namespace CrediSoporte\Modules\Attachments\Controllers;

use CrediSoporte\Domain\Models\Attachment;
use CrediSoporte\Domain\Request\Request;
use CrediSoporte\Domain\Session\Session;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

// Logica migrada desde app/Controllers/UpdateAttachmentController.php
class UpdateAttachmentController
{
    public static function handle(): void
    {
session_start();

$request = new Request();
$session = new Session();

$attachment = Attachment::find($request->post('id'));

if (is_null($attachment)) {
    header('Location:' . \CrediSoporte\Core\Bootstrap::root() . '/admin/actualizarFormato.php');
    exit();
}

$validator = Validation::createValidator();
$input = [
    'name' => $request->post('name'),
    'description' => $request->post('description'),
    'status' => $request->post('status')
];
$groups = new Assert\GroupSequence(['Default', 'custom']);
$constraint = new Assert\Collection([
    'name' => new Assert\NotBlank(['message' => 'Seleccione un archivo.']),
    'description' => [
        new Assert\NotBlank(['message' => 'La descripción no puede estar vacia.']),
        new Assert\Length(['max' => 500, 'maxMessage' => 'La descripcion no debe contener mayor a {{ limit }} caracteres.'])
    ],
    'status' => new Assert\Choice(['choices' => ['true', 'false'], 'message' => 'Seleccione el estado.'])
]);

$violations = $validator->validate($input, $constraint, $groups);

if (0 !== count($violations)) {
    // hay errores ahora puedes mostrarlos
    $errors = $violations;
    $session->setFlash('errors', $errors);
} else {
    if ($attachment->name === $request->post('name')) {
        $attachment->update([
            'description' => $request->post('description'),
            'expiration_at' => $request->post('expiration_at'),
            'status' => $request->post('status') === 'true' ? true : false
        ]);

        $session->setFlash('message', 'Archivo actualizado con exito!!');
    } else {
        // eliminamos el archivo antiguo
        if (file_exists(\CrediSoporte\Core\Bootstrap::root() . '/storage/attachments/' . $attachment->name)) {
            unlink(\CrediSoporte\Core\Bootstrap::root() . '/storage/attachments/' . $attachment->name);
        }

        $currentLocation = \CrediSoporte\Core\Bootstrap::root() . '/storage/uploads/' . $request->post('name');
        $newLocation = \CrediSoporte\Core\Bootstrap::root() . '/storage/attachments/' . $request->post('name');

        if (rename($currentLocation, $newLocation)) {
            $attachment = $attachment->update([
                'path' => $_ENV['APP_URL'] . '/storage/attachments/',
                'name' => $request->post('name'),
                'name_original' => $request->post('name_original'),
                'extension' => $request->post('extension'),
                'description' => $request->post('description'),
                'expiration_at' => $request->post('expiration_at', null),
                'status' => $request->post('status') == 'true' ? true : false
            ]);

            $session->setFlash('message', 'Archivo actualizado con exito!!');
        } else {
            $session->setFlash('error', 'No encontramos el archivo. Por favor vuelva a intentar.');
        }
    }
}

$_SESSION['flash']['inputs'] = $_POST;
header('Location:' . \CrediSoporte\Core\Bootstrap::root() . '/admin/actualizarFormato.php?attachmentId=' . $request->post('id'));
    }
}
