<?php

namespace CrediSoporte\Modules\Credits\Legacy;

use CrediSoporte\Domain\Models\Credit;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/justifyNonPayment.php.
// El wrapper en app/api/justifyNonPayment.php preserva URL, entradas y salida legacy.
class JustifyNonPayment
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

if (!$request->user()) {
    echo json_encode([
        'message' => 'Tienes que iniciar sesión.',
        'success' => false
    ]);
    die();
}

$credit = Credit::find($request->credit_id);

if (!$credit) {
    echo json_encode([
        'message' => 'El credito no existe.',
        'success' => false
    ]);
    die();
}

$errors = $request->validate([
    'category_id' => 'required|exists',
    'coordinate_lat' => 'nullable|numeric|digits_between:5,15',
    'coordinate_lng' => 'nullable|numeric|digits_between:5,15'
], [
    'category_id.required' => 'La categoria es obligatorio.',
    'category_id.exists' => 'La categoria seleccionada no es válida.'
], [
    'category_id.exists' => $database->table('justify_types')->where('id', $request->category_id)->exists()
]);

if (count($errors) > 0) {
    echo json_encode([
        'errors' => $errors,
        'success' => false
    ]);
    die();
}

$request->description = trim($request->description);

if (!empty($request->image) && file_exists(\CrediSoporte\Core\Bootstrap::root() . '/storage/uploads/'. $request->image)) {
    mkdir(\CrediSoporte\Core\Bootstrap::root() . '/storage/justifications');
    rename(\CrediSoporte\Core\Bootstrap::root() . '/storage/uploads/' . $request->image, \CrediSoporte\Core\Bootstrap::root() . '/storage/justifications/' . $request->image);
}

$result = $database->table('non_payment_justifications')
    ->insert([
        'user_id' => $request->user()->idU,
        'credit_id' => $request->credit_id,
        'description' => $request->description,
        'created_at' => date('Y-m-d H:i:s'),
        'coordinate_lat' => $request->coordinate_lat,
        'coordinate_lng' => $request->coordinate_lng,
        'image_url' => $request->image,
        'category_id' => $request->category_id
    ]);

echo json_encode([
    'data' => $result,
    'message' => 'Registrado con exito!!',
    'success' => true
]);
    }
}
