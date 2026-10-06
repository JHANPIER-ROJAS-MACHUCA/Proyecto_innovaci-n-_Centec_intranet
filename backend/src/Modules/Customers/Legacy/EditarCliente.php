<?php

namespace CrediSoporte\Modules\Customers\Legacy;

use CrediSoporte\Domain\Models\Customer;
use CrediSoporte\Domain\Models\User;
use CrediSoporte\Domain\Request\Request;

// Logica migrada verbatim desde app/api/editarCliente.php.
// El wrapper en app/api/editarCliente.php preserva URL, entradas y salida legacy.
class EditarCliente
{
    public static function handle(): void
    {$request = new Request();

if (empty($_POST['mod_id'])) {
    $errors[] = "ID vacío";
} else if (empty($_POST['mod_dni'])) {
    $errors[] = "Dni vacío";
} else if (empty($_POST['mod_celular'])) {
    $errors[] = "Celular vacío";
} else if (
    !empty($_POST['mod_id']) &&
    !empty($_POST['mod_dni']) &&

    !empty($_POST['mod_celular'])
) {

    try {
        $user = User::findOrFail($request->post('mod_idusu'));

        Customer::where('idCG', $request->post('mod_id'))
            ->update([
                'risk_profile_id' => $request->post('risk_profile_id', null),
                'client_type' => $request->post('client_type', null),
                'dni' => trim($request->post('mod_dni')),
                'direc' => trim($request->post('mod_direccion')),
                'correo' => trim($request->post('mod_correo')),
                'telefono' => trim($request->post('mod_telefono')),
                'rubro' => trim($request->post('mod_rubro')),
                'cel' => trim($request->post('mod_celular')),
                'nom' => trim($request->post('mod_nombres')),
                'ap' => trim($request->post('mod_ap')),
                'am' => trim($request->post('mod_am')),
                'fec_nac' => trim($request->post('mod_fecha')),
                'n_hijos' => trim($request->post('mod_nhijos')),
                'grado_inst' => trim($request->post('mod_grado')),
                'estado_civil' => trim($request->post('mod_ecivil')),
                'lugar_nac' => trim($request->post('mod_lugarnac')),
                'referencia' => trim($request->post('mod_referencias')),
                'tipo' => trim($request->post('mod_tipo')),
                'comentario' => trim($request->post('mod_comentario')),
                'sexo' => $request->post('mod_sexo'),
                'idU' => $request->post('mod_idusu'),
                'idO' => $user->idO,
                'status' => $request->post('status', null)
            ]);

        $messages[] = "Cliente ha sido actualizado satisfactoriamente.";
    } catch (\Throwable $th) {
        $errors[] = "Error desconocido." . $th->getMessage();
    }
} else {
    $errors[] = "Error desconocido.";
}

if (isset($errors)) {
            echo '<div class="alert alert-danger" role="alert">';
            echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
            echo '<strong>Error!</strong>';
            foreach ($errors as $error) {
                echo $error;
            }
            echo '</div>';
        }
        if (isset($messages)) {
            echo '<div class="alert alert-success" role="alert">';
            echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
            echo '<strong>&iexcl;Bien hecho!</strong>';
            foreach ($messages as $message) {
                echo $message;
            }
            echo '</div>';
        }
    }
}
