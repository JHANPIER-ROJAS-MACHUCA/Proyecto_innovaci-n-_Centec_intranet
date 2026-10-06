<?php

namespace CrediSoporte\Modules\Attachments\Controllers;

use CrediSoporte\Domain\Models\Attachment;
use CrediSoporte\Domain\Request\Request;
use CrediSoporte\Domain\Session\Session;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

// Logica migrada desde app/Controllers/CreateAttachmentController.php
class CreateAttachmentController
{
    public static function handle(): void
    {
session_start();

$request = new Request();
$session = new Session();

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
    $session->setFlash('inputs', $_POST);
} else {
    $currentLocation = \CrediSoporte\Core\Bootstrap::root() . '/storage/uploads/' . $request->post('name');
    $newLocation = \CrediSoporte\Core\Bootstrap::root() . '/storage/attachments/' . $request->post('name');

    if (rename($currentLocation, $newLocation)) {
        $attachment = Attachment::create([
            'path' => $_ENV['APP_URL'] . '/storage/attachments/',
            'name' => $request->post('name'),
            'name_original' => $request->post('name_original'),
            'extension' => $request->post('extension'),
            'description' => $request->post('description'),
            'expiration_at' => $request->post('expiration_at', null),
            'status' => $request->post('status') == 'true' ? true : false
        ]);

        $session->setFlash('message', 'Archivo subido con exito!!');
    } else {
        $session->setFlash('error', 'No encontramos el archivo. Por favor vuelva a intentar.');
        $session->setFlash('inputs', $_POST);
    }
}

header('Location:' . \CrediSoporte\Core\Bootstrap::root() . '/admin/formato.php');
    }
}
