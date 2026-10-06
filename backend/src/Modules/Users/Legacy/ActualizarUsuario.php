<?php

namespace CrediSoporte\Modules\Users\Legacy;

use CrediSoporte\Domain\Models\User;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/actualizarUsuario.php.
// El wrapper en app/api/actualizarUsuario.php preserva URL, entradas y salida legacy.
class ActualizarUsuario
{
    public static function handle(): void
    {
        global $database;
$request = new Request();

$user = User::find($request->idU);

if (!$user) {
    echo json_encode([
        'message' => 'El usuario no existe',
        'success' => false
    ]);
    die();
}

$errors = $request->validate([
    'dniU' => 'required|digits:8|unique',
    'apU' => 'required|max:30',
    'amU' => 'required|max:30',
    'nomU' => 'required|max:30',
    'celU' => 'nullable|numeric|digits:9',
    'direcU' => 'nullable|max:200',
    'correoU' => 'nullable|email|max:100',
    'tipoU' => 'required|in:1,2,3,4',
    'idO' => 'required|exists',
    'estadoU' => 'required|in:1,2',
    'birthdate' => 'nullable|date'
], [], [
    'idO.exists' => $database->table('toficina')->where('idO', $request->idO)->exists(),
    'dniU.unique' => $database->table('tusuario')->where('dniU', $request->dniU)->where('idU', '!=', $request->idU)->exists(),
]);

if (count($errors) > 0) {
    echo json_encode([
        'errors' => $errors,
        'success' => false
    ]);
    die();
}

// fin de la validacion

if ($user->img != $request->img) {
    if (isset($request->img) && $request->img) {
        $currentLocation = \CrediSoporte\Core\Bootstrap::root() . '/storage/uploads/' . $request->img;
        $newLocation = \CrediSoporte\Core\Bootstrap::root() . '/storage/profiles/' . $request->img;

        // si no existe el archivo o no se logro mover
        if (!file_exists($currentLocation) || !rename($currentLocation, $newLocation)) {
            $request->img = null;
        }
    }
}

$user->update($request->only([
    'dniU',
    'apU',
    'amU',
    'nomU',
    'celU',
    'direcU',
    'correoU',
    'tipoU',
    'idO',
    'img',
    'estadoU',
    'birthdate'
]));
if ($request->idO) {
    $office = $database->table('toficina')->where('idO', $request->idO)->first();
    $user->direccion = $office->direccion;
}

echo json_encode([
    'data' => $user,
    'success' => true
]);
    }
}
